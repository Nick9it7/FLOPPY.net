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

    public function subscriptionsAction()
    {

    }

    public function photoAction()
    {
        if ($this->request->hasFiles() == true) {
            $upload = $this->request->getUploadedFiles();
            $id = $this->session->get('user_identity')['id'];

            /**
             * @var Users $user
             */
            $user = Users::findFirst(
                [
                    'id = :id:',
                    'bind' => [
                        'id' => $id,
                    ]
                ]
            );
            $path = '/public/img/userImages/user_avatar_' . $id . '_' . md5($upload[0]->getName()) . '.' . $upload[0]->getExtension();

            if ($user->hasPhoto()) {
                unlink(BASE_PATH . $user->getPhoto());
                $user->setPhoto(null);
                $user->save();
            }

            if ($upload[0]->moveTo(BASE_PATH . $path)) {
                $user->setPhoto($path);
                $user->save();

                return $this->response->setJsonContent($path);
            }
        }
    }
}