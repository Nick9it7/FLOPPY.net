<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class Users
 */
class Users extends Model
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $photo;

    /**
     * @return bool
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'email',
            new Email(
                [
                    'message' => 'Email введений невірно'
                ]
            )
        );
        $validator->add(
            'email',
            new Uniqueness(
                [
                    'message' => 'Вибачте. Email вже зареєстрований іншим користувачем'
                ]
            )
        );

        $validator->add(
            'name',
            new Uniqueness(
                [
                    'message' => 'Вибачте. Користувач з таким ім\'ям вже зареєстрований'
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Declared relationships many to one
     */
    public function initialize()
    {
        $this->hasMany(
            'id',
            'PasswordRecovery',
            'user'
        );

        $this->hasOne(
            'id',
            'Subscription',
            'user'
        );

        $this->hasMany(
            'id',
            'Note',
            'user'
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return bool
     */
    public function hasPhoto()
    {
        return ($this->getPhoto() !== null) ? true : false;
    }

    /**
     * @param $user
     * @return bool
     */
    public function hasSubscribed($sesseionUser,$user)
    {
        $subscribe = Subscription::findFirst(
            [
                'subscriber = :subscriber: AND user = :user:',
                'bind' => [
                    'subscriber' => $sesseionUser,
                    'user' => $user
                ]
            ]
        );

        if ($subscribe === false) {
            return false;
        } else {
            return true;
        }
    }
}