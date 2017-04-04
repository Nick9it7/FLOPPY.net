<?php

use Phalcon\Mvc\Controller;

class NoteController extends Controller
{
    public function createAction()
    {
        if ($this->request->isPost()) {


        }
    }

    public function showAction()
    {
        if ($this->request->isPost()) {

            /**
             * @var Users $user
             */
            $user = Users::findFirst(
                [
                    'name = :name:',
                    'bind' => [
                        'name' => $this->request->getPost('name'),
                    ]
                ]
            );
            
            $this->view->user = $user;
        }
    }
}