<?php

use Phalcon\Mvc\Controller;

class FileController extends Controller
{

    public function indexAction()
    {

    }

    public function downloadAction()
    {

    }

    public function uploadAction()
    {
        #check if there is any file
        if ($this->request->hasFiles() == true) {
            $uploads = $this->request->getUploadedFiles();
            $isUploaded = false;
            #do a loop to handle each file individually
            foreach ($uploads as $upload) {
                #define a “unique” name and a path to where our file must go
                $path = BASE_PATH . '/cache/' . md5(uniqid(rand(), true)) . '-' . strtolower($upload->getname());
                #move the file and simultaneously check if everything was ok
                ($upload->moveTo($path)) ? $isUploaded = true : $isUploaded = false;

            }
        }
    }
}