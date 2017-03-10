<?php

namespace Application\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Subscription
 * @package Application\Model
 * @ORM\Entity
 * @ORM\Table(name="subscription")
 */
class Subscription
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\Column(type="integer")
     */
    private $followers;

    /**
     * @var User
     * @ORM\Column(type="integer")
     */
    private $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return User
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param User $followers
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}