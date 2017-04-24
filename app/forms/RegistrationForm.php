<?php


use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\Validation\Validator\StringLength;

class RegistrationForm extends ValidForm
{
    public function initialize()
    {
        $email = new Text('email');
        $email->setLabel('Email');
        $this->filter($email);
        $this->requiredValidator($email);
        $this->add($email);


        $name = new Text('name');
        $name->setLabel('Логін');
        $this->filter($name);
        $this->requiredValidator($name);
        $this->add($name);


        $password = new Password('password');
        $password->setLabel('Пароль');
        $this->filter($password);
        $this->requiredValidatorCancel($password);
        $password->addValidators(
            [
                new StringLength(
                    [
                        'min' => 6,
                        'max' => 48,
                        'messageMaximum' => 'Пароль не повинен перевищувати 48 символів',
                        'messageMinimum' => 'Пароль повинен бути довжиною не менше 6 символів',
                    ]
                )
            ]
        );
        $this->add($password);


        $conformPassword = new Password('confirmPassword');
        $conformPassword->setLabel('Підтвердіть пароль');
        $this->filter($conformPassword);
        $conformPassword->addValidators(
            [
                new Confirmation(
                    [
                        "message" => "Пароль не збігається з підтвердженням",
                        "with"    => "password",
                    ]
                )
            ]
        );
        $this->add($conformPassword);
    }
}