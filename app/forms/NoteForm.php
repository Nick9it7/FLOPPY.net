<?php


use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\TextArea;

class NoteForm extends ValidForm
{
    public function initialise()
    {
        $file = new Hidden('file');
        $file->setLabel('File');
        $this->requiredValidator($file);
        $this->add($file);

        $textarea = new TextArea('decs');
        $textarea->setLabel('Description');
        $this->requiredValidator($textarea);
        $this->add($textarea);

    }
}