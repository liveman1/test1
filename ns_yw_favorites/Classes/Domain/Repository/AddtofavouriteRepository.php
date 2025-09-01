<?php

declare(strict_types=1);

namespace NITSAN\NsYwFavorites\Domain\Repository;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Connection;

/**
 * This file is part of the "Test" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023
 */

/**
 * The repository for Addtofavourites
 */
class AddtofavouriteRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
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

     /**
     * @param $tableName string
     */
    private function getConnectionPool(string $tableName): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);
    }

    public function addList($title, $description, $user, $pic, $uid, $defaultpic, $username){
        $pic = isset($pic) ? $pic : 0;
        $defaultpic = isset($defaultpic) ? $defaultpic : 0;
        $tableConnectionCategoryMM = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $tstamp = time();
        // $myResult = $queryBuilder->count('uid')
        //     ->from('tx_nsywfavorites_domain_model_addtofavourite')
        //     ->where(
        //         $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($title))
        //     )
        //     ->executeQuery()
        //     ->fetchOne();
        if($uid){
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
        if($copyRecord){
            $title = $copyRecord[0]['name'].' (copy)';
            $description = $copyRecord[0]['desc'];
            $user = $user;
            $pic = $copyRecord[0]['pic'];
            $contain = $copyRecord[0]['contain'];
            $defaultpic = $copyRecord[0]['defaultpic'];
            $editable = $copyRecord[0]['editable'];
            $username = $copyRecord[0]['username'];
        }
        // if($myResult > 0){
        //     $err = array("Error"=>"List Already Exists");
        //     echo json_encode($err);die;
        // } else {
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
        // }
    }

    public function addFavoriteListFromBasket($title, $description, $user, $contain, $defaultpic, $username, $name){
        $defaultpic = isset($defaultpic) ? $defaultpic : 0;
        $tableConnectionCategoryMM = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $existingRecord = $queryBuilder->select('*')
        ->from('tx_nsywfavorites_domain_model_addtofavourite')
        ->where(
            $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name))
        )
        ->andWhere(
            $queryBuilder->expr()->eq('user', $queryBuilder->createNamedParameter($user, Connection::PARAM_INT)),
            $queryBuilder->expr()->eq('contain', $queryBuilder->createNamedParameter($contain)),
        )
        ->executeQuery()
        ->fetchAllAssociative();
        if (count($existingRecord) <= 0) {
            $tstamp = time();
            $editable = $user;
            $data[] = [
                'name' => $title,
                'pic' => '0',
                'user' => $user,
                'desc' => $description,
                'contain' => $contain,
                'defaultpic' => $defaultpic,
                'editable' => $editable,
                'tstamp' => $tstamp,
                'username' => $username,
            ];
            $tableConnectionCategoryMM->bulkInsert(
                'tx_nsywfavorites_domain_model_addtofavourite',
                array_values($data),
                ['name', 'pic', 'user', 'desc', 'contain', 'defaultpic', 'editable', 'tstamp', 'username']
            );
            return 1;
        }
    }

    public function checked($checked,$uid){
        // 10 = Editable
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_urls');

        $copyRecord = $queryBuilder->select('user')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        if($checked == '10'){
            $queryBuilder
                ->update('tx_nsywfavorites_domain_model_addtofavourite')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                    )
                )
                ->set('editable', $copyRecord[0]['user'])
                ->set('editabletoall', 1)
                ->executeStatement();
        }
        if($checked == '11'){
            $str_arr = explode (",", $copyRecord[0]['user']);
            $queryBuilder
                ->update('tx_nsywfavorites_domain_model_addtofavourite')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                    )
                )
                ->set('editable', $str_arr[0])
                ->set('editabletoall', 0)
                ->executeStatement();
        }
        return;
    }

    public function findOrigurl($crypt){
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_urls');
        $origurl = $queryBuilder->select('origurl')
        ->from('tx_nsywfavorites_urls')
        ->where(
            $queryBuilder->expr()->eq(
                'crypticurl',
                $queryBuilder->createNamedParameter($crypt)
            )
        )
        ->executeQuery()
        ->fetchAllAssociative();
        return $origurl;
    }

    public function unsubscribe($uid,$user){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        $subscribeList = $queryBuilder->select('user')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
        $str_arr = explode (",", $subscribeList[0]['user']);
        $pos = array_search($user, $str_arr);
        if ($pos !== false) {
            unset($str_arr[$pos]);
        }
        $List = implode(', ', $str_arr);
        $queryBuilder
            ->update('tx_nsywfavorites_domain_model_addtofavourite')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->set('user', $List)
            ->executeStatement();
        return;

    }

    public function updateList($title, $description, $user, $pic, $uid){
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
    public function findAllLists($userId){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
        ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');


        $users = $queryBuilder->select('user','uid')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
            ->executeQuery()
            ->fetchAllAssociative();
        $arr = [];
        foreach($users as $user){
            $str_arr = explode (",", $user['user']);
            if(in_array($userId, $str_arr)){
                $myRes = $queryBuilder->select('*')
                ->from('tx_nsywfavorites_domain_model_addtofavourite')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($user['uid'], \PDO::PARAM_INT)
                        )
                    )
                ->executeQuery()
                ->fetchAllAssociative();
                array_push($arr,$myRes);
            }
        }
        $myResult = [];
        foreach($arr as $myRes){
            $myResult[] = $myRes[0];
        }
        if($myResult){
            $resultRows = $this->getConnectionPool('sys_file');
            $queryBuilder1 = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_file');
            for($i = 0; $i <= count($myResult); $i++){
                if(isset($myResult[$i]['pic'])){
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
        }
        //check Editable, Not-Editable or Admin
        if($myResult){
            for($i=0; $i<count($myResult); $i++){
                $myResult[$i]['userArr'] = explode (",", $myResult[$i]['user']);
                $myResult[$i]['editbleArr'] = explode (",", $myResult[$i]['editable']);

            }
            $myResult = $this->sortByLatest($myResult);
        }
        return $myResult;
    }

    public function updatePageList($contain,$id){
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');
        $tstamp = time();

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
                        ->set('tstamp', $tstamp)
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
                        ->set('tstamp', $tstamp)
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
        $resultRows = $this->getConnectionPool('tx_nsywfavorites_domain_model_addtofavourite');
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $myResult = $queryBuilder->select('name')
            ->from('tx_nsywfavorites_domain_model_addtofavourite')
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

    public function sortByLatest($myResult){
        usort($myResult, function ($item1, $item2) {
            return $item2['tstamp'] <=> $item1['tstamp'];
        });
        return $myResult;
    }

    /**
     * fetchAllData
     *
     * @param string $ordering
     * @param string|null $user
     * @param string $orderingField
     * @return array
     */
    public function fetchAllData(string $ordering, string $orderingField = '', string $user = null): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_nsywfavorites_domain_model_addtofavourite');

        $queryBuilder->select('uid', 'username', 'user', 'defaultpic', 'pic', 'name','desc','contain')
            ->from('tx_nsywfavorites_domain_model_addtofavourite');
        if ($orderingField == 'uid') {
            $queryBuilder->orderBy('uid',$ordering);
        } elseif ($orderingField == 'username') {
            $queryBuilder->orderBy('username',$ordering);
        }
        if($user) {
            $queryBuilder->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($user, Connection::PARAM_STR)),
            );
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * fetchAllData
     *
     * @return array|null
     * @throws DBALException
     * @throws Exception
     */
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
}
