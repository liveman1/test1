<?php

declare(strict_types=1);

namespace NITSAN\NsYwFavorites\Domain\Repository;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
 * The repository for favourites
 */
class favouriteRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    public function updateSysFileReferenceRecord(int $uid_local, string $table, $fields) {
        $tableConnectionCategoryMM = $this->getConnectionPool('sys_file_reference');
        $sysFileReferenceData[$uid_local] = [
            'uid_local' => $uid_local,
            'tablenames' => $table,
            'fieldname' => 'pic',
            'sorting_foreign' => 1,
            'table_local' => 'sys_file'
        ];
        if (!empty($sysFileReferenceData)) {
            $tableConnectionCategoryMM->bulkInsert(
                'sys_file_reference',
                array_values($sysFileReferenceData),
                ['uid_local', 'tablenames', 'fieldname', 'sorting_foreign','table_local']
            );
        }
        return $uid_local;
    }

    public function sliderPages($id){
        $tableConnectionCategoryMM = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $contains = $queryBuilder->select('contain')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchOne();
        if($contains){
            $str_arr = explode (",", $contains); 
            $tableConnectionCategoryMM = $this->getConnectionPool('pages');
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable('pages');
            foreach($str_arr as $page){

                $pages[] = $queryBuilder->select('*')
                    ->from('pages')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($page, \PDO::PARAM_INT)
                        )
                    )
                    ->executeQuery()
                    ->fetchAllAssociative();
            } 
            if($pages){
                $pages['page'] = $id;
            }
            return $pages;
        }
    }
     /**
     * @param $tableName string
     */
    private function getConnectionPool(string $tableName): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
    }

    public function shareList($userId, $id, $editable){
        $tableConnectionCategoryMM = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        // Check if user shared with is the same user
        $user = $queryBuilder->select('user','editable')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        if(!$user){
            // $addedIn = array('Err' => 'Something went wrong. Please contact Admin!');
            // echo json_encode($addedIn);die;
            return "err";
        }
        if(strpos($user[0]['user'], ",") !== false){
            $str_arr = explode (",", $user[0]['user']); 
            if(in_array($userId, $str_arr)){
                return LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:share_list_already_added');
            } else {
                if($editable){
                    $editable = $user[0]['editable'].','.$userId;
                } else {
                    $editable = $user[0]['editable'];
                }
                $user = $user[0]['user'].','.$userId;
                $queryBuilder
                    ->update('tx_nsywfavorites_domain_model_addtofavourite')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                        )
                    )
                    ->set('user', $user)
                    ->set('editable', $editable)
                    ->executeStatement();
                // $addedIn = array('succ' => 'List Saved');
                // echo json_encode($addedIn);die;
                return LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:share_list_added');
            }
        } else {
            if($editable){
                $editable = $user[0]['editable'].','.$userId;
            } else {
                $editable = $user[0]['editable'];
            }
            $user = $user[0]['user'].','.$userId;
            $queryBuilder
                ->update('tx_nsywfavorites_domain_model_addtofavourite')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                    )
                )
                ->set('user', $user)
                ->set('editable', $editable)
                ->executeStatement();
            return LocalizationUtility::translate('LLL:EXT:ns_yw_favorites/Resources/Private/Language/locallang.xlf:share_list_added');
        }
    }

    public function updateUrl($origUrl,$curl){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_urls');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_urls');

        $queryBuilder
            ->update('tx_nsywfavorites_urls')
            ->where(
                $queryBuilder->expr()->eq(
                    'crypticurl',
                    $queryBuilder->createNamedParameter($curl)
                )
            )
            ->set('origUrl', $origUrl)
            ->executeStatement();
        return $origUrl;
    }
    public function addList($title, $description, $user, $pic, $uid, $editable, $defaultpic, $username){
        $pic = isset($pic) ? $pic : 0;
        $defaultpic = isset($defaultpic) ? $defaultpic : 0;
        $tableConnectionCategoryMM = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        // if($action == 'duplicateList'){
        //     $copyRecord = $queryBuilder->select('*')
        //     ->from('tx_nsywfavorites_domain_model_addtofavourite')
        //     ->where(
        //         $queryBuilder->expr()->eq(
        //             'uid',
        //             $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
        //         )
        //     )
        //     ->executeQuery()
        //     ->fetchAllAssociative();
        //     if($copyRecord){
        //         \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($copyRecord,__FILE__.''.__LINE__);
        //         die;
        //     }
        // }
        $tableConnectionCategoryMM = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        if($_REQUEST['tx_nsywfavorites_pi2']['action'] == 'duplicateList'){
            $copyRecord = $queryBuilder->select('*')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        }
        $contain = '';
        $tstamp = time();
        if($copyRecord){
            $title = $copyRecord[0]['name'].' (copy)';
            $description = $copyRecord[0]['desc'];
            $user = $user; 
            $pic = $copyRecord[0]['pic'];
            $contain = $copyRecord[0]['contain'];
            $defaultpic = $copyRecord[0]['defaultpic'];
            $username = $copyRecord[0]['username'];
        }
        $editable = $user;
            $data[] = [
                'name' => $title,
                'pic' => $pic,
                'user' => $user,
                'desc' => $description,
                'contain' => $contain,
                'defaultpic' => $defaultpic,
                'editable' => $editable,
                'tstamp' => $tstamp,
                'username' => $username
            ];

            $tableConnectionCategoryMM->bulkInsert(
                'tx_nsywfavorites_domain_model_addtofavourite',
                array_values($data),
                ['name', 'pic', 'user', 'desc', 'contain', 'defaultpic', 'editable', 'tstamp', 'username']
            );
            return;
    }

    public function checkUrl($uid){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_urls');
        $existingUrl = $queryBuilder->select('crypticurl')
            ->from('tx_nsywfavorites_urls')
            ->where(
                $queryBuilder->expr()->eq(
                    'listid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        return $existingUrl;
    }

    public function addUrl($origUrl,$crypt,$uid){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_urls');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_urls');

        $data[] = [
            'origurl' => $origUrl,
            'crypticurl' => $crypt,
            'listid' => $uid,
        ];

        $resultRows->bulkInsert(
            'tx_nsywfavorites_urls',
            array_values($data),
            ['origurl', 'crypticurl', 'listid']
        );
        return $crypt;
    }
    public function updateList($title, $description, $pic, $uid){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        if(!$pic){
            $oldPic = $queryBuilder->select('pic')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
            $pic = $oldPic[0]['pic'];
        }
        $tstamp = time();
        
        $queryBuilder
            ->update('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->set('name', $title)
            ->set('pic', $pic)
            ->set('desc', $description)
            ->set('tstamp', $tstamp)
            ->executeStatement();
        return;
    }
    public function findAllLists(){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        $this->updateDeletedUsers();
        
        $myResult = $queryBuilder->select('*')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->executeQuery()
            ->fetchAllAssociative();
        if($myResult){
            $resultRows = $this->getConnectionPool('sys_file');
            $queryBuilder1 = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');
            for($i = 0; $i <= count($myResult); $i++){
                if($myResult[$i]['pic'] > 0){
                    $imagepath = $queryBuilder1->select('identifier')
                        ->from('sys_file')
                        ->where(
                            $queryBuilder1->expr()->eq(
                                'uid',
                                $queryBuilder1->createNamedParameter($myResult[$i]['pic'], \PDO::PARAM_INT)
                            )
                        )
                        ->executeQuery()
                        ->fetchAllAssociative();
                    $myResult[$i]['imagePath'] = $imagepath;
                }
            }
        }
        return $myResult;
    }

    public function updatePageList($contain,$id){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $myResult = $queryBuilder->select('contain')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        
        if($myResult[0]['contain']){
            if(strpos($myResult[0]['contain'], ",") !== false){
                $str_arr = explode (",", $myResult[0]['contain']); 
                if(in_array($contain, $str_arr)){
                    $addedIn = array('Err' => $this->addedFunc($id));
                    echo json_encode($addedIn);die;
                } else {
                    $contain = $myResult[0]['contain'].','.$contain;
                    $queryBuilder
                        ->update('tx_nsywfavorites_domain_model_addtofavourite')
                        ->where(
                            $queryBuilder->expr()->eq(
                                'uid',
                                $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                            )
                        )
                        ->set('contain', $contain)
                        ->executeStatement();
                        $addedIn = array('succ' => $this->addedFunc($id));
                        echo json_encode($addedIn);die;
                }
            } else{
                if($myResult[0]['contain'] == $contain){
                    $addedIn = array('Err' => $this->addedFunc($id));
                    echo json_encode($addedIn);die;
                } else {
                    $contain = $myResult[0]['contain'].','.$contain;
                    $queryBuilder
                        ->update('tx_nsywfavorites_domain_model_addtofavourite')
                        ->where(
                            $queryBuilder->expr()->eq(
                                'uid',
                                $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                            )
                        )
                        ->set('contain', $contain)
                        ->executeStatement();
                    $addedIn = array('succ' => $this->addedFunc($id));
                    echo json_encode($addedIn);die;
                }
            }
        } else {
            $queryBuilder
                ->update('tx_nsywfavorites_domain_model_addtofavourite')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                    )
                )
                ->set('contain', $contain)
                ->executeStatement();
                $addedIn = array('succ' => $this->addedFunc($id));
                echo json_encode($addedIn);die;
        }
    }

    public function addedFunc($id){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_favourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_favourite');
         
        $myResult = $queryBuilder->select('name')
            ->from('tx_nsywfavorites_domain_model_favourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($id, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
            return $myResult[0]['name'];
    }

    public function deletePage($id,$listId){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $myResult = $queryBuilder->select('contain')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($listId, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $str_arr = explode (",", $myResult[0]['contain']); 
        if (($key = array_search($id, $str_arr)) !== false) {
            unset($str_arr[$key]);
        }
        $List = implode(',', $str_arr);

        $queryBuilder
            ->update('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($listId, \PDO::PARAM_INT)
                )
            )
            ->set('contain', $List)
            ->orderBy('tstamp')
            ->executeStatement();
        return;
    }

    public function leftPage($id,$listId,$orientation){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $myResult = $queryBuilder->select('contain')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($listId, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        $str_arr = explode (",", $myResult[0]['contain']); 
        
        if (($key = array_search($id, $str_arr)) !== false && $orientation == 'left') {
            if($key > 0){
                $upper = $str_arr[$key-1];
                
                $bellow = $str_arr[$key];
                $str_arr[$key] = $upper;
                $str_arr[$key-1] = $bellow;
            } else {
                $err = array("Error"=>"Already First !!");
                echo json_encode($err);die;
            }
        }

        if (($key = array_search($id, $str_arr)) !== false && $orientation == 'right') {
            if($key >= count($str_arr)-1){
                $err = array("Error"=>"Already Last !!");
                echo json_encode($err);die;
            } else {
                $upper = $str_arr[$key+1];
                $bellow = $str_arr[$key];
                $str_arr[$key] = $upper;
                $str_arr[$key+1] = $bellow;
            } 
        }
        $List = implode(',', $str_arr);
        $queryBuilder
            ->update('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($listId, \PDO::PARAM_INT)
                )
            )
            ->set('contain', $List)
            ->executeStatement();
        return;
    }

    public function reorderPages(int $listId)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        $contain = $queryBuilder->select('contain')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($listId, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchOne();

        if (!$contain) {
            return;
        }

        $pages = GeneralUtility::intExplode(',', $contain, true);
        if (!$pages) {
            return;
        }

        $sortPaths = [];
        $cache = [];
        foreach ($pages as $uid) {
            $sortPaths[$uid] = $this->getSortPathForPage($uid, $cache);
        }

        asort($sortPaths, SORT_STRING);
        $ordered = array_keys($sortPaths);

        if ($ordered) {
            $newContain = implode(',', $ordered);
            $queryBuilder
                ->update('tx_nsywfavorites_domain_model_addtofavourite')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($listId, \PDO::PARAM_INT)
                    )
                )
                ->set('contain', $newContain)
                ->executeStatement();
        }

        return;
    }

    protected function getSortPathForPage(int $pageId, array &$cache): string
    {
        if (isset($cache[$pageId])) {
            return $cache[$pageId];
        }

        $qb = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('pages');
        $row = $qb
            ->select('pid', 'sorting')
            ->from('pages')
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($pageId, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative();

        if (!$row) {
            $cache[$pageId] = '';
            return '';
        }

        $path = '';
        if ((int)$row['pid'] > 0) {
            $path .= $this->getSortPathForPage((int)$row['pid'], $cache);
        }

        $path .= str_pad((string)$row['sorting'], 10, '0', STR_PAD_LEFT) . '.';
        $cache[$pageId] = $path;
        return $path;
    }

    public function updateDeletedUsers(){
        $resultRows = $this->getConnectionPool('fe_users');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_users');
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
        $deleted = $queryBuilder->select('uid')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder1 = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        if($deleted[0]){
            foreach($deleted as $act){
                $queryBuilder1
                    ->update('tx_nsywfavorites_domain_model_addtofavourite')
                    ->where(
                        $queryBuilder1->expr()->eq(
                            'user',
                            $queryBuilder1->createNamedParameter($act['uid'], \PDO::PARAM_INT)
                        )
                    )
                    ->set('deleted', 1)
                    ->executeStatement();
            }
        }
        return;
    }

    public function getUserGroups($uid)
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

    public function getUserId($uid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        return $queryBuilder
            ->select('user')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_STR))
            )
            ->executeQuery()
            ->fetchOne();
    }
}
