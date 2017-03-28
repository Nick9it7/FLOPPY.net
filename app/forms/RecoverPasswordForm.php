<?php

use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\StringLength;

class RecoverPasswordForm extends ValidForm
{
    public function initialize()
    {
        $password = new Password('password');
        $password->setLabel('Password');
        $this->filter($password);
        $this->requiredValidatorCancel($password);
        $password->addValidators(
            [
                new StringLength(
                    [
                        'min' => 6,
                        'max' => 48,
                    ]
                )
            ]
        );
        $this->add($password);
    }
}