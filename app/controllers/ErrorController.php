<?php

use Phalcon\Mvc\Controller;

class ErrorController extends Controller
{
    public function notFoundAction()
    {
        $this->response->setStatusCode(404, 'Not Found');
        $this->flashSession->error('Not found page');
        $this->dispatcher->forward(
            [
                'controller' => 'index',
                'action' => 'index'
            ]
        );
    }
}