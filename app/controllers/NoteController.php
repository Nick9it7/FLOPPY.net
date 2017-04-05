<?php

use Phalcon\Mvc\Controller;

class NoteController extends Controller
{
    public function createAction()
    {
        if ($this->request->isPost()) {
            $form = new NoteForm();
            $error = [];

            if ($form->isValid($this->request->getPost()) === true) {

            } else {
                foreach ($form->getMessages() as $message)
                    $error[] = [
                        'field' => $message->getField(),
                        'message' => $message->getMessage()
                    ];

            }
            $this->response->setJsonContent([
                'error' => $error
            ]);
            return $this->response;
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

            if ($user === false) {
                //return 'user not found';
            } else {
                $this->view->user = $user;
            }

        }
    }
}