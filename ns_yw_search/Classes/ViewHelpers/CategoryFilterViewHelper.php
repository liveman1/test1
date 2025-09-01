<?php
namespace NITSAN\NsYwSearch\ViewHelpers;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class CategoryFilterViewHelper extends AbstractTagBasedViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
    }

    public function render()
    {
        $context = GeneralUtility::makeInstance(Context::class);
        $feUserId = $context->getPropertyFromAspect('frontend.user', 'id');
        if ($feUserId > 0) {
            $currentUserGroup = $this->getUserGroups($feUserId);
            $registeredGroup = $this->getRegisteredUserGroup();
            $currentUserGroupArray = explode(',',$currentUserGroup);
            $commonElements = array_intersect($registeredGroup, $currentUserGroupArray);
            if(!empty($commonElements)) {
                return true;
            }
        }
        return false;
    }

    private function getRegisteredUserGroup()
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $setting = $configurationManager->getConfiguration('FullTypoScript', 'ns_yw_search');
        $registeredUsergroup = '';
        if(isset($setting['ns_yw_search.']['website.']['settings.']['categoryFilterUserGroup'])) {
            $registeredUsergroup = $setting['ns_yw_search.']['website.']['settings.']['categoryFilterUserGroup'];
        }
        return explode(',',$registeredUsergroup);
    }

    private function getUserGroups($uid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_users');
        return $queryBuilder
            ->select('usergroup')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_STR))
            )
            ->executeQuery()
            ->fetchOne();
    }
}