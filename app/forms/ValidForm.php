<?php

use Phalcon\Forms\Form;
use Phalcon\Validation\Validator\PresenceOf;

class ValidForm extends Form
{
    public function filter(Phalcon\Forms\Element $element)
    {
        $element->setFilters(
            [
                'string',
                'trim'
            ]
        );
    }

    public function requiredValidator(Phalcon\Forms\Element $element)
    {
        $element->addValidators(
            [
                new PresenceOf()
            ]
        );
    }

}