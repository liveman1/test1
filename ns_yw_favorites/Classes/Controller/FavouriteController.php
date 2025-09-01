<?php

declare(strict_types=1);

namespace NITSAN\NsYwFavorites\Controller;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Context\UserAspectInterface;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use NITSAN\NsYwFavorites\Property\TypeConverter\UploadedFileReferenceConverter;


/**
 * This file is part of the "Test" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023
 */

/**
 * FavouriteController
 */
class FavouriteController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var UserAspectInterface
     */
    protected $frontendUser;

    /**
     * Injects the context with the frontend user
     *
     * @param Context $context
     */
    public function injectContext(Context $context): void
    {
        $this->frontendUser = $context->getAspect('frontend.user');
    }

    /**
     * pageRenderer
     *
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    public function injectPageRenderer(PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * favouriteRepository
     *
     * @var \NITSAN\NsYwFavorites\Domain\Repository\FavouriteRepository
     */
    protected $favouriteRepository = null;

    /**
     * @param \NITSAN\NsYwFavorites\Domain\Repository\FavouriteRepository $favouriteRepository
     */
    public function injectFavouriteRepository(\NITSAN\NsYwFavorites\Domain\Repository\FavouriteRepository $favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
    }

    /**
     * PageRepository
     *
     * @var \TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    protected $pageRepository = null;

    /**
     * @param \TYPO3\CMS\Core\Domain\Repository\PageRepository $pageRepository
     */
    public function injectPageRepository(\TYPO3\CMS\Core\Domain\Repository\PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * addtofavouriteRepository
     *
     * @var \NITSAN\NsYwFavorites\Domain\Repository\AddtofavouriteRepository
     */
    protected $addtofavouriteRepository = null;

    /**
     * @param \NITSAN\NsYwFavorites\Domain\Repository\AddtofavouriteRepository $addtofavouriteRepository
     */
    public function injectAddtofavouriteRepository(\NITSAN\NsYwFavorites\Domain\Repository\AddtofavouriteRepository $addtofavouriteRepository)
    {
        $this->addtofavouriteRepository = $addtofavouriteRepository;
    }

    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->configurationManager = $objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $config = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $this->constantSettings = $config['settings'];
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $id = 0;
        if(isset(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['myParameter'])){
            $this->view->assign('errFlag', 1);
        }
        if(isset(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['clickon'])){
            $action = GeneralUtility::_GP('tx_nsywfavorites_pi2')['clickon'] ?? null;
            $listId = (int) GeneralUtility::_GP('tx_nsywfavorites_pi2')['listId'];
            if ($action === 'reorder') {
                $this->favouriteRepository->reorderPages($listId);
            } elseif (in_array($action, ['left','right'], true)) {
                $id = (int) GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
                $this->favouriteRepository->leftPage($id,$listId,$action);
            }
        }

        if(isset(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'])){
            $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
        }
        if(isset(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['listId'])){
            $id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['listId'];
        }
        $currentListId = $id;
        if($id){
            $pages = $this->favouriteRepository->sliderPages($id);
            $this->view->assign('pages', $pages);
        }
        $editableListArray = [];
        if ($currentListId) {
            $currentList = $this->addtofavouriteRepository->findByUid($currentListId);
            if ($currentList) {
                if ($editableList = $currentList->getEditable()) {
                    $editableListArray = GeneralUtility::trimExplode(',', $editableList);
                }
            }
        }
        $userId = $this->frontendUser->get('id');
        if ($editableListArray) {
            foreach ($editableListArray as $editableUser) {
                if ($editableUser == $userId) {
                    $this->view->assign('isEditable', '1');
                }
            }
        }
        $favourites = $this->addtofavouriteRepository->findAllLists($userId);
        $this->view->assign('userId', $userId);
        $this->view->assign('subtitle', $GLOBALS['TSFE']->page['subtitle']);
        $this->view->assign('uid', $GLOBALS['TSFE']->id);
        $this->view->assign('favourites', $favourites);

        return $this->htmlResponse();

    }

    /**
     * Media fileProceessig.
     *
     * @param array $media
     * @return array
     */
    protected function fileProceessig($media)
    {
        // Initializing:
        /** @var \TYPO3\CMS\Core\Utility\File\ExtendedFileUtility $fileProcessor */
        $fileProcessor = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Utility\\File\\ExtendedFileUtility');
        $fileProcessor->setActionPermissions(array('addFile' => TRUE));
        $fileProcessor->setExistingFilesConflictMode(DuplicationBehavior::RENAME);
        // Actual upload
        $fileProcessor->start($media);
        $result = $fileProcessor->processData();
        return $result;
    }

    /**
     * action create
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Favourite $newFavourite
     */
    public function createAction(\NITSAN\NsYwFavorites\Domain\Model\Favourite $newFavourite)
    {
        $username = $GLOBALS['TSFE']->fe_user->user['username'];
        if(isset($this->arguments['newFavourite'])){
            $uidLocal = [];
            if(!$_FILES['tx_nsywfavorites_pi2']['name']['pic']){
                $defaultPic = '/typo3conf/ext/ns_yw_favorites/Resources/Public/Icons/list.png';
            } else{
                $media = [];
                $namespace = key($_FILES);
                $targetFalDirectory = '1:/user_upload/';
                // $this->registerUploadField($media, $namespace, 'pic', $targetFalDirectory);
                if (!isset($media['upload'])) {
                    $media['upload'] = array();
                }
                $counter = count($media['upload']) + 1;

                $keys = array_keys($_FILES[$namespace]);

                foreach ($keys as $key) {
                    $_FILES['upload_' . $counter][$key] = $_FILES[$namespace][$key]['pic'];
                }
                $media['upload'][$counter] = array(
                    'data' => $counter,
                    'target' => $targetFalDirectory,
                );
                // func end
                $fileAddedresult = $this->fileProceessig($media);
                //Relationship between sys_file_reference and tx_avertas_domain_model_application...
                if($fileAddedresult['upload']){
                    foreach ($fileAddedresult['upload'][0] as $file) {
                        $uidLocal[] = $this->favouriteRepository->updateSysFileReferenceRecord($file->getUid(), 'tx_nsywfavorites_domain_model_addtofavourite',$newFavourite);
                    }
                }
            }
        }
        if(!$newFavourite->getName()){
            $addedIn = array('Error' => LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:name_field_required'));
            echo json_encode($addedIn);die;
        }
        $this->favouriteRepository->addList($newFavourite->getName(),$newFavourite->getDesc(), $newFavourite->getUser(), $uidLocal[0], '', 0, $defaultPic, $username);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Favourite $favourite
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("favourite")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\NITSAN\NsYwFavorites\Domain\Model\Favourite $favourite): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('favourite', $favourite);
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Favourite $updatefavourite
     */
    public function updateAction(\NITSAN\NsYwFavorites\Domain\Model\Favourite $updatefavourite)
    {
        if(isset($this->arguments['updatefavourite'])){
            $media = [];
            $namespace = key($_FILES);
            $targetFalDirectory = '1:/user_upload/';
            // $this->registerUploadField($media, $namespace, 'pic', $targetFalDirectory);
            if (!isset($media['upload'])) {
                $media['upload'] = array();
            }
            $counter = count($media['upload']) + 1;

            if($_FILES){
                $keys = array_keys($_FILES[$namespace]);
            }

            foreach ($keys as $key) {
                $_FILES['upload_' . $counter][$key] = $_FILES[$namespace][$key]['pic'];
            }
            $media['upload'][$counter] = array(
                'data' => $counter,
                'target' => $targetFalDirectory,
            );
            // func end
            $fileAddedresult = $this->fileProceessig($media);
            //Relationship between sys_file_reference and tx_avertas_domain_model_application...
            if($fileAddedresult['upload']){
                foreach ($fileAddedresult['upload'][0] as $file) {
                    $uidLocal[] = $this->favouriteRepository->updateSysFileReferenceRecord($file->getUid(), 'tx_nsywfavorites_domain_model_favourite',$updatefavourite);
                }
            }
            if(!$updatefavourite->getName()){
                $addedIn = array('Error' => LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:name_required'));
                echo json_encode($addedIn);die;
            }
            $this->favouriteRepository->updateList($updatefavourite->getName(),$updatefavourite->getDesc(), $uidLocal[0], $updatefavourite->getUid());
        }
        $this->redirect('list');
    }

    /**
     * action update
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite
     */
    public function addpagetolistAction(\NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite)
    {
        $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
        $this->addtofavouriteRepository->updatePageList($addtofavourite->getContain(),$id);
        $this->redirect('list');
    }

    /**
     * action update
     *
     */
    public function duplicateListAction()
    {
        $editable = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['editable'];
        $userId = $this->frontendUser->get('id');
        if(!$userId){
            echo LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:login_first');
            die;
        }

        $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
        $this->favouriteRepository->addList('','', $userId, '', $id, $editable, '','');
        $this->redirect('list');
    }

    /**
     * action update
     *
     */
    public function shareAction()
    {
        $editable = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['editable'];
        $userId = $this->frontendUser->get('id');
        if(!$userId){
            $redirectPID = (int)$this->constantSettings['redirectPID'];
            $uriBuilder = $this->uriBuilder;
            $uri = $uriBuilder->reset()
                ->setTargetPageUid($redirectPID)->build();
            \TYPO3\CMS\Core\Utility\HttpUtility::redirect($uri.'?shareactionres='.LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:login_first'));
        }

        $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
        $result = $this->favouriteRepository->shareList($userId, $id, $editable);
        if($result == 'err'){
            $uriBuilder = $this->uriBuilder;
            $uri = $uriBuilder->reset()
                ->setTargetPageUid($GLOBALS['TSFE']->id)
                ->uriFor(
                    'listAction',
                    ['myParameter' => 'ERR'],
                    'FavouriteController',
                    'NsYwFavorites',
                    'tx_nsywfavorites_pi2'
                );

            \TYPO3\CMS\Core\Utility\HttpUtility::redirect($uri, \TYPO3\CMS\Core\Utility\HttpUtility::HTTP_STATUS_302);
        } else {
            $redirectPID = (int)$this->constantSettings['redirectPID'];
            $uriBuilder = $this->uriBuilder;
            $uri = $uriBuilder->reset()
                ->setTargetPageUid($redirectPID)->build();

            \TYPO3\CMS\Core\Utility\HttpUtility::redirect($uri.'?shareactionres='.$result);
        }
    }

    /**
     * action update
     *
     */
    public function crypticurlAction()
    {
        if(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('url')){
            $origUrl = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('url');
            $lastTwoChars = substr($origUrl, -2);
            $uid = (int)strtok(substr($origUrl, strpos($origUrl, "[uid]=") + 6), '&');
            if($lastTwoChars == '10' || $lastTwoChars == '11'){
                $this->addtofavouriteRepository->checked($lastTwoChars,$uid);
            }
            $sortUrl = substr($origUrl, strpos($origUrl, "?") + 1);
            $domain = strtok($origUrl, '?');
            $outUrl = $this->favouriteRepository->checkUrl($uid);
            if (is_array($outUrl) && isset($outUrl[0]['crypticurl'])) {
                $this->favouriteRepository->updateUrl($origUrl,$outUrl[0]['crypticurl']);
                $outUrl = $outUrl[0]['crypticurl'];
            } else {
                $bytes = random_bytes(20);
                $crypt = bin2hex($bytes);
                $outUrl = $this->favouriteRepository->addUrl($origUrl,$crypt,$uid);
            }
            $newDomain = str_replace('favourite','add-to-favourite',$domain);
            $outUrl = $newDomain.'?encurl='.$outUrl;
            $json = json_encode($outUrl);
            $json = str_replace(':\/\/', '://', $json);
            $json = str_replace('"', '', $json);
            $json = stripslashes($json);
            echo $json;die;
        } else {
            echo LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:somethins_wrong');
        }
    }

    /**
     * action update
     *
     */
    public function deletePageAction()
    {
        $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
        $listId = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['listId'];
        $this->favouriteRepository->deletePage($id,$listId);
        $this->redirect('list');
    }

    /**
     * action update
     *
     */
    public function unfollowAction()
    {
        $uid = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
        $user = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['user'];
        $this->addtofavouriteRepository->unsubscribe($uid,$user);
        $this->redirect('list');
    }

    /**
     * action update
     *
     */
    public function rightPageAction()
    {
        $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['uid'];
        $listId = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi2')['listId'];
        $this->favouriteRepository->rightPage($id,$listId);
    }

     /**
     * action delete
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite
     */
    public function deleteAction(\NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite)
    {
        $this->addtofavouriteRepository->remove($addtofavourite);
        $this->redirect('list');
    }

    /**
     * action sharedList
     *
     */
    public function sharedListAction()
    {
        $this->addInlineSettings();
        $sharedList = [];
        if((int)$this->settings['showAll'] == 0) {
            $maxResult = (int)$this->settings['itemPerPage'];
        } else {
            $maxResult = null;
        }
        $addToFavPage = $this->pageRepository->getPage((int)$this->settings['addToFavPid']);
        $addToFavSlug = $addToFavPage['slug'];
        $favPage =  $this->pageRepository->getPage((int)$this->settings['favPid']);
        $favPageSlug =  $favPage['slug'];
        $ordering = $this->settings['ordering'];
        $orderingField = $this->settings['orderingField'];
        if(isset($this->request->getParsedBody()['selectetOption']) && $this->request->getParsedBody()['selectetOption'] != 'all'){
            $list = $this->addtofavouriteRepository->fetchAllData($ordering, $orderingField, $this->request->getParsedBody()['selectetOption']);
        } else {
            $list = $this->addtofavouriteRepository->fetchAllData($ordering, $orderingField);
        }
        $feUsers = [];
        foreach($list as &$listItem) {
            $totalUser = $listItem['user'];
            if($listItem['username'] != '') {
                $feUsers[] = $listItem['username'];
            }
            $countUser = explode(',',$totalUser);
            $listItem['totalCount'] = count($countUser) - 1;
        }

        if ($orderingField == 'shared') {
            if ($ordering == 'ASC') {
                usort($list, function ($a, $b) {
                    return $a['totalCount'] <=> $b['totalCount'];
                });

            }
            if ($ordering == 'DESC') {
                usort($list, function ($a, $b) {
                    return $b['totalCount'] <=> $a['totalCount'];
                });
            }
        }
        foreach($list as $listItem) {
            if($maxResult != null) {
                if(count($sharedList) <= $maxResult - 1 ) {
                    $sharedList[] = $listItem;
                }
            } else {
                $sharedList[] = $listItem;
            }
        }

        $this->view->assignMultiple([
            'sharedList' => $sharedList,
            'addToFavSlug' => $addToFavSlug,
            'favPageSlug' => $favPageSlug,
            'feUsers' =>  array_unique($feUsers)
        ]);
    }

    protected function setTypeConverterConfigurationForImageUpload($argumentName)
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \TYPO3\CMS\Extbase\Domain\Model\FileReference::class,
                \NITSAN\NsYwFavorites\Domain\Model\FileReference::class
            );
            $uploadConfiguration = [
                UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
                UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/user_upload/',
            ];
            /** @var PropertyMappingConfiguration $newProductInquiryConfiguration */
            $newProductInquiryConfiguration = $this->arguments[$argumentName]->getPropertyMappingConfiguration();
            $newProductInquiryConfiguration->forProperty('pic')
            ->setTypeConverterOptions(
                UploadedFileReferenceConverter::class,
                $uploadConfiguration
            );
    }

    /**
     * @return void
     */
    private function addInlineSettings()
    {
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId($GLOBALS['TSFE']->id);
        $context = GeneralUtility::makeInstance(Context::class);
        $id = $context->getPropertyFromAspect('language', 'id');
        $languages = $site->getLanguages();
        $currentLang = null;
        if (!empty($languages)) {
            foreach($languages as $a){
                if ($a->getLanguageId() == $id) {
                    $currentLang = $a;
                }
            }
        }
        if ($currentLang) {
            $entryPoint = $currentLang->getBase()->getPath();
            $renderer = GeneralUtility::makeInstance(PageRenderer::class);
            $renderer->addInlineSetting(null,'ENTRY_POINT',$entryPoint);

            // Add inline labels to the settings
            $renderer->setCharSet('utf-8');
            $renderer->addInlineLanguageLabelFile('EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf');
            $renderer->addInlineLanguageLabelFile('EXT:ns_yw_shop/Resources/Private/Language/locallang.xlf');
        }
    }
}
