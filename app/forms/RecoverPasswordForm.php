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
                        'min'            => 6,
                        'max'            => 48,
                        'messageMaximum' => 'Пароль не повинен перевищувати 48 символів',
                        'messageMinimum' => 'Пароль повинен бути довжиною не менше 6 символів',
                    ]
                )
            ]
        );
        $this->add($password);
    }
}