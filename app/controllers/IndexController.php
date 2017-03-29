<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{

    public function indexAction()
    {
        if ($this->session->has('user_identity')) {
            $gravatar = $this->getDi()->getShared('gravatar');
            $this->view->url = $gravatar->getAvatar('john@doe.com');
        } else {
            $this->response->redirect('/user/login');
        }
    }
}