<?php

use Phalcon\Mvc\Model;

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
     * @var array
     */
    private $files = [];

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
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }
}