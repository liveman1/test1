<?php

namespace NITSAN\NsYwFavorites\Domain\Model;


/**
 * This file is part of the "Test" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2023 
 */

/**
 * favourite
 */
class Favourite extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     *
     * @var string
     */
    protected $name = null;

    /**
     * uid
     *
     * @var int
     */
    protected $uid = 0;

    /**
     * contain
     *
     * @var string
     */
    protected $contain = null;
    
    /**
     * desc
     *
     * @var string
     */
    protected $desc = null;
    
    /**
     * user
     *
     * @var int
     */
    protected $user = null;

    /**
     * username
     *
     * @var string
     */
    protected $username = '';

    /**
     * pic
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $pic = null;

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return void
     */
    public function setUserName(string $username)
    {
        $this->username = $username;
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the contain
     *
     * @return string
     */
    public function getContain()
    {
        return $this->contain;
    }

    /**
     * Sets the contain
     *
     * @param string $contain
     * @return void
     */
    public function setContain(string $contain)
    {
        $this->contain = $contain;
    }

    /**
     * Returns the desc
     *
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * Sets the desc
     *
     * @param string $desc
     * @return void
     */
    public function setDesc(string $desc)
    {
        $this->desc = $desc;
    }

    /**
     * Returns the user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the user
     *
     * @param int $user
     * @return void
     */
    public function setUser(int $user)
    {
        $this->user = $user;
    }

    /**
     * Returns the pic
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getPic()
    {
        return $this->pic;
    }

    /**
     * Sets the pic
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $pic
     * @return void
     */
    public function setPic(\TYPO3\CMS\Extbase\Domain\Model\FileReference $pic)
    {
        $this->pic = $pic;
    }


    /**
     * Sets the uid
     *
     * @param int $uid
     * @return void
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }
}
