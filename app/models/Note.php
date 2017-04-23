<?php

use Phalcon\Mvc\Model;
use Phalcon\Validation;

class Note extends Model
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $user;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $file;

    /**
     * Declared relationships many to one
     */
    public function initialize()
    {
        $this->belongsTo(
            'user',
            'Users',
            'id'
        );
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'file',
            new Email(
                [
                    'message' => 'Email введений невірно'
                ]
            )
        );
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $files
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}