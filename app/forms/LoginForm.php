<?php

use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Email;

class LoginForm extends ValidForm
{

    public function initialize()
    {
        $login = new Text('email');
        $login->setLabel('Email');
        $this->filter($login);
        $this->requiredValidator($login);
        $this->add($login);


        $password = new Password('password');
        $password->setLabel('Password');
        $this->filter($password);
        $this->requiredValidator($password);
        $this->add($password);
    }
}