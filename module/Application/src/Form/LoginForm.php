<?php

namespace Application\Form;


use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('login-form');

        $this->setAttribute('method', 'post');

        $this->setAttribute('action', 'user/login');

        
    }
}