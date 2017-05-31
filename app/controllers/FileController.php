<?php

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;

class FileController extends Controller
{
    /**
     * @var \Dropbox\Client
     */
    private $dbxClient;

    /**
     * @var string
     */
    private $removePath;

    /**
     * @var string
     */
    private $localPath;

    public function initialize()
    {
        $config = $this->di->getConfig();
        $this->dbxClient = new \Dropbox\Client($config->dropbox->access, "PHP-FLOPPY.net/1.0");
        $this->removePath = '/user_' . $this->session->get('user_identity')['id'];
        $this->localPath = BASE_PATH . '/cache/temp';
    }

    public function indexAction()
    {

    }

    public function downloadAction()
    {
        if ($this->request->isPost()) {
            $r = fopen($this->localPath, 'w+');
            $fileMetadata = $this->dbxClient->getFile($this->request->getPost('file'), $r);
            fclose($r);

            $response = new Response();
            $response->setHeader("Cache-Control", 'must-revalidate, post-check=0, pre-check=0');
            $response->setHeader("Content-Description", 'File Download');
            $response->setHeader("Content-Type", $fileMetadata['mime_type']);
            $response->setHeader("Content-Length", $fileMetadata['bytes']);
            $response->setFileToSend($this->localPath, $this->request->getPost('fileName'), true);
            $response->send();

            unlink($this->localPath);
            die();
        }
    }

    public function uploadAction()
    {
        if ($this->request->hasFiles() == true) {
            $uploads = $this->request->getUploadedFiles();

            foreach ($uploads as $upload) {

                $upload->moveTo($this->localPath);
                $name = md5(uniqid(rand(), true));
                $this->dbxClient->createFolder($this->removePath);


                    $r = fopen($this->localPath,'r');
                    $this->dbxClient->uploadFile($this->removePath . '/' . $name , \Dropbox\WriteMode::add(), $r);
                    $this->session->set('cache_file',[
                        'name' => $name
                    ]);
                    fclose($r);

                unlink($this->localPath);
            }
        }
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            if ($this->request->getPost('file') !== '') {
                $file = $this->session->get('cache_file')['name'];
                $this->dbxClient->delete($this->removePath . '/' . $file);
            }
        }
    }
}