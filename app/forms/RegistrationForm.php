<?php


use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\StringLength;

class RegistrationForm extends ValidForm
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
                        'message' => 'Email is invalid'

                    ]
                )
            ]
        );
        $this->add($email);


        $name = new Text('name');
        $name->setLabel('Name');
        $this->filter($name);
        $this->requiredValidator($name);
        $this->add($name);


        $password = new Password('password');
        $password->setLabel('Password');
        $this->filter($password);
        $this->requiredValidator($password);
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