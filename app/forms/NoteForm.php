<?php


use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;
use Phalcon\Validation\Validator\PresenceOf;

class NoteForm extends ValidForm
{
    public function initialize()
    {
        $file = new Hidden('titleFile');
        $file->setLabel('Файл');
        $file->addValidators(
            [
                new PresenceOf(
                    [
                        'message' => 'Виберіть файл'
                    ]
                )
            ]
        );
        $this->add($file);

        $text = new TextArea('desc');
        $text->setLabel('Опис файлу');
        $this->requiredValidator($text);
        $this->add($text);
    }
}