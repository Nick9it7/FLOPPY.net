<?php

use Phalcon\Mvc\Model;

class Subscription extends Model
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $subscriber;

    /**
     * @var int
     */
    private $user;

    /**
     * Declared relationships many to one
     */
    public function initialize()
    {
        $this->hasOne(
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
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param int $subscriber
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
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


}