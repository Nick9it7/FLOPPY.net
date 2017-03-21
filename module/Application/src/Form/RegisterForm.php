<?php

namespace Application\Form;

use Zend\Form\Form;

class RegisterForm extends Form
{
    public function __construct()
    {
        parent::__construct('register-form');

        $this->setAttribute('method', 'post');

        $this->setAttribute('action', 'user/register');

        
    }
}