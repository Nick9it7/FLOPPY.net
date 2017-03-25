<?php


use Phalcon\Mvc\Model;

class PasswordRecovery extends Model
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $user;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var bool
     */
    private $active;

    /**
     * Declared relationships many to one
     */
    public function initialize()
    {
        $this->belongsTo(
            'user',
            'Users',
            'id'
        );
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = (int)$active;
    }

    /**
     * @return string
     */
    public static function generateHash()
    {
        return sha1(uniqid(static::class));
    }
}