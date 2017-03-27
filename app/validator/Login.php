<?php

use Phalcon\Validation;
use Phalcon\Validation\Message;


class Login extends Validation
{
    /**
     * @var Users
     */
    private $usera;

    /**
     * @param $data
     * @param Users $entity
     * @return bool|Validation\Message\Group
     */
    public function validate($data = null, $entity = null)
    {
        /**
         * @var Users $user
         */
        $user = $entity->findFirst(
            [
                'email = :email:',
                'bind' => [
                    'email' => $data['email'],
                ]
            ]
        );

        if ($user !== false) {
            $password = $data['password'];
            if ($this->security->checkHash($password, $user->getPassword())) {
                return true;
            }
        }
        //$this->appendMessage($this->message);
        return false;
    }

    /**
     * @return Users
     */
    public function getUser()
    {
        return $this->user;
    }
}