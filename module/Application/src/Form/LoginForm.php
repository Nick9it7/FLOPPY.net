<?php

namespace Application\Form;

use Zend\Form\Element\Email;
use Zend\Form\Element\Password;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    public function __construct()
    {
        $this->add(
            [
                'type' => Email::class,
                'name' => 'email',
            ]
        );

        $this->add(
            [
                'type' => Password::class,
                'name' => 'password',
            ]
        );

        $this->addInputFilter();
    }

    private function addInputFilter()
    {
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        $inputFilter->add([
            'name'     => 'email',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'password',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 5,
                        'max' => 16
                    ],
                ],
            ],
        ]);
    }
}