<?php

use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Email;

class ForgotPasswordForm extends ValidForm
{
    public function initialize()
    {
        $email = new Text('email');
        $email->setLabel('Email');
        $this->filter($email);
        $this->requiredValidator($email);
        $email->addValidators(
            [
                new Email(
                    [
                        'message' => 'Email is not valid'
                    ]
                )
            ]
        );
        $this->add($email);
    }
}