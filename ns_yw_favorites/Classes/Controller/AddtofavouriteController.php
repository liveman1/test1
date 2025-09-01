<?php

declare(strict_types=1);

namespace NITSAN\NsYwFavorites\Controller;

use NITSAN\NsYwFavorites\Property\TypeConverter\UploadedFileReferenceConverter;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspectInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;


/**
 * This file is part of the "Test" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 
 */

/**
 * AddtofavouriteController
 */
class AddtofavouriteController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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


    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        if(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('encurl')){
            $crypt = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('encurl');
            $origurl = $this->addtofavouriteRepository->findOrigurl($crypt);
            if($origurl[0]){
                $origurl[0]['origurl'];
                // \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($origurl[0]['origurl'],__FILE__.''.__LINE__);die;
                $this->redirectToUri($origurl[0]['origurl'], 0, 301);
            } else {
                echo LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:somethins_wrong');die;
            }
        }
        $userId = $this->frontendUser->get('id');
        $addtofavourites = $this->addtofavouriteRepository->findAllLists($userId);
        $this->view->assign('userId', $userId);
        $this->view->assign('subtitle', $GLOBALS['TSFE']->page['subtitle']);
        $this->view->assign('uid', $GLOBALS['TSFE']->id);
        $this->view->assign('addtofavourites', $addtofavourites);
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
     * @param \NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $newAddtofavourite
     */
    public function createAction(\NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $newAddtofavourite)
    {
        $username = $GLOBALS['TSFE']->fe_user->user['username'];
        if(isset($this->arguments['newAddtofavourite'])){
            $uidLocal = [];
            if(!$_FILES['tx_nsywfavorites_pi1']['name']['pic']){
                $defaultPic = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi1')['defaultPic'];
            } else {
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
                        $uidLocal[] = $this->addtofavouriteRepository->updateSysFileReferenceRecord($file->getUid(), 'tx_nsywfavorites_domain_model_addtofavourite',$newAddtofavourite);
                    }
                }
            }
            if(!$uidLocal[0] && !$defaultPic){
                $addedIn = array('Error' => LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:image_error'));
                echo json_encode($addedIn);die;
            }
        }

        if(!$newAddtofavourite->getName()){
            $addedIn = array('Error' => LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:name_field_required'));
            echo json_encode($addedIn);die;
        }
        $this->addtofavouriteRepository->addList($newAddtofavourite->getName(),$newAddtofavourite->getDesc(), $newAddtofavourite->getUser(), $uidLocal[0], '', $defaultPic, $username);        
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("addtofavourite")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('addtofavourite', $addtofavourite);
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $updatefavourite
     */
    public function updateAction(\NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $updatefavourite)
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
                    $uidLocal[] = $this->addtofavouriteRepository->updateSysFileReferenceRecord($file->getUid(), 'tx_nsywfavorites_domain_model_addtofavourite',$updatefavourite);
                }
            }
        }
        if(!$updatefavourite->getName()){
            $addedIn = array('Error' => LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:name_required'));
            echo json_encode($addedIn);die;
        }
        $this->addtofavouriteRepository->updateList($updatefavourite->getName(),$updatefavourite->getDesc(), $updatefavourite->getUser(), $uidLocal[0], $updatefavourite->getUid());        
        $this->redirect('list');
    }

    /**
     * action update
     *
     * @param \NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite
     */
    public function addpagetolistAction(\NITSAN\NsYwFavorites\Domain\Model\Addtofavourite $addtofavourite)
    {
        $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi1')['uid'];
        $this->addtofavouriteRepository->updatePageList($addtofavourite->getContain(),$id);
        $this->redirect('list');
    }

    /**
     * action update
     *
     */
    public function duplicateListAction()
    {
        $id = (int) \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_nsywfavorites_pi1')['uid'];
        $this->addtofavouriteRepository->addList('','',$this->frontendUser->get('id'), '', $id, '','');        
        $this->redirect('list');
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
}
