<?php


use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Validation\Validator\File;

class NoteForm extends ValidForm
{
    public function initialize()
    {
        $file = new Hidden('file');
        $file->setLabel('Файл');
        $this->requiredValidator($file);
        $this->add($file);

        $text = new Text('desc');
        $text->setLabel('Опис файлу');
        $this->requiredValidator($text);
        $this->add($text);
    }
}