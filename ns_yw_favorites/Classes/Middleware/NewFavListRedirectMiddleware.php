<?php

namespace NITSAN\NsYwFavorites\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;

class NewFavListRedirectMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $userId = $request->getAttribute('frontend.user')->user['uid'] ?? null;
        $languageServiceFactory = GeneralUtility::makeInstance(
            LanguageServiceFactory::class
        );
        $languageService = $languageServiceFactory->createFromSiteLanguage(
            $request->getAttribute('language') ?? $request->getAttribute('site')->getDefaultLanguage(),
        );

        $requestHost= $request->getAttribute('normalizedParams')->getRequestHost();
        $baseUrl = $requestHost . $request->getAttribute('language')->getBase()->getPath();
        
        // Redirect if the URI contains '/newfavlist'
        if (strpos($uri, '/newfavlist') !== false && $userId) {
            $parameters = $request->getUri()->getQuery();

            $convertPagesToArray = GeneralUtility::trimExplode('+', $parameters);

            $title = str_replace('-',' ',$convertPagesToArray[0]);
            $key = array_search($convertPagesToArray[0], $convertPagesToArray);
            if ($key !== false) {
                unset($convertPagesToArray[$key]);
                $convertPagesToArray = array_values($convertPagesToArray);
            }
            foreach ($convertPagesToArray as $value) {
                $favPageFromUrl[] =  $this->fetchAllPages('/' . $value);
            }

            $checkNull = array_filter($favPageFromUrl, function($value) {
                return $value !== null;
            });

            $uids = array_column($favPageFromUrl, 'uid');
            $contain = implode(',', $uids);
            $description = '';

            if(!empty($checkNull)){

                $userName = $request->getAttribute('frontend.user')->user['username'];
                $defaultPic = '/typo3conf/ext/ns_yw_favorites/Resources/Public/Icons/list.png';
                $languageId = $request->getAttribute('language')->getLanguageId();

                $response =  $this->addFavoriteListFromBasket(
                    $title, $description, $userId, $contain, $defaultPic, $userName, $languageId, $title);

                $messageKey = match ($response) {
                    'Added' => 'share_list_added',
                    'Updated' => 'share_list_updated',
                    default => 'share_list_already_added'
                };
                $this->tostMeMessage($languageService->sL('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:'. $messageKey), $baseUrl);
            } else {
                $this->tostMeMessage($languageService->sL('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:no.page.found'), $baseUrl);
            }
        } else {
            $this->tostMeMessage($languageService->sL('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:tx_nsywfavorites_domain_model_addtofavourite.logedout'), $baseUrl);
        }

        // Pass the request to the next middleware
        return $handler->handle($request);
    }

    public function fetchAllPages($pageTitle): ?array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');

        $record = $queryBuilder
            ->select('uid', 'slug')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->eq('slug', $queryBuilder->createNamedParameter($pageTitle, Connection::PARAM_STR))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, Connection::PARAM_STR)),
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, Connection::PARAM_STR))
            )
            ->executeQuery()
            ->fetchAssociative();

        if ($record === false) {
            return null;
        }

        return $record;
    }

    public function addFavoriteListFromBasket($title, $description, $user, $contain, $defaultPic, $username, $languageId, $name): string{
        $defaultPic = isset($defaultPic) ? $defaultPic : 0;

        $tableConnectionCategoryMM = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        $existingRecord = $queryBuilder->select('*')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name))
            )
            ->andWhere(
                $queryBuilder->expr()->eq('user', $queryBuilder->createNamedParameter($user, Connection::PARAM_INT)),
            )
            ->executeQuery()
            ->fetchAllAssociative();
        if (count($existingRecord) <= 0) {
            $tstamp = time();
            $editable = $user;
            $queryBuilder
                ->insert('tx_nsywfavorites_domain_model_addtofavourite')
                ->values([
                    'name' =>  $title,
                    'pic' => '0',
                    'user' => $user,
                    'desc' => $description,
                    'contain' => $contain,
                    'defaultpic' => $defaultPic,
                    'editable' => $editable,
                    'tstamp' => $tstamp,
                    'username' => $username,
                    'sys_language_uid' => $languageId
                ])
                ->executeStatement();
            return 'Added';
        }
        else{
            $existingPages = GeneralUtility::trimExplode(',', $existingRecord[0]['contain']);
            $requestPages = GeneralUtility::trimExplode(',', $contain);
            $updateContain = array_diff($requestPages, $existingPages);
            
            // Check if the difference array is empty
            if (empty($updateContain)) {
                return 'Already added';
            } else {
                $updatePages = $existingRecord[0]['contain'] .','. implode(',', $updateContain);
                $queryBuilder
                    ->update('tx_nsywfavorites_domain_model_addtofavourite')
                    ->where(
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($existingRecord[0]['uid'], Connection::PARAM_INT))
                    )
                    ->set('contain', $updatePages)
                    ->executeStatement();
                return 'Updated';
            }
        }
    }

    public function tostMeMessage($msg, $baseUrl){

        echo "
            <script>
                // Store the toast message in a cookie and redirect the page
                document.cookie = 'toastMessage=' + encodeURIComponent('" . addslashes($msg) . "') + '; path=/; max-age=3600'; // 1 hour expiration
                window.location = '$baseUrl';
            </script>
        ";
    }
}
