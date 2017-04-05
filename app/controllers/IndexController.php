<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{

    public function indexAction()
    {
        if ($this->session->has('user_identity')) {

            $user = Users::findFirst(
                [
                    'id = :id:',
                    'bind' => [
                        'id' => $this->session->get('user_identity')['id']
                    ]
                ]
            );
            $this->view->user = $user;
        } else {
            $this->response->redirect('/user/login');
        }
    }

    public function searchAction()
    {
        /**
         * @var Users $user
         */
        $user = Users::find();

        $name = [];
        foreach ($user as $item) {
            $name[] = $item->getName();
        }
        return $this->response->setJsonContent($name);
    }
}