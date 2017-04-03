<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{

    public function indexAction()
    {
        if ($this->session->has('user_identity')) {

        } else {
            $this->response->redirect('/user/login');
        }
    }

    public function searchAction()
    {
        $gravatar = $this->getDi()->getShared('gravatar');

        /**
         * @var Users $user
         */
        $user = Users::find();

        $name = [];
        foreach ($user as $item) {
            $name['url'][] =  $gravatar->getAvatar($item->getEmail());
            $name['name'][] = $item->getName();
        }
        return $this->response->setJsonContent($name);
    }
}