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

            $expansion = [];
            $articles = $user->note;
            $pattern = '#\.[a-z]*$#';

            foreach ($articles as $note) {
                $name = $note->getFile();
                preg_match($pattern, $name, $expansion[]);
            }

            $src = [];
            foreach ($expansion as $exp) {
                if (in_array($exp, NoteController::$doc)) $src[] = '';
                elseif (in_array($exp, NoteController::$images)) $src[] = '';
                elseif (in_array($exp, NoteController::$music)) $src[] = '';
                elseif (in_array($exp, NoteController::$video)) $src[] = '';
                elseif (in_array($exp, NoteController::$archive)) $src[] = '';
            }

            $this->view->user = [
                'user' => $user
            ];
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
            if ($item->getName() === $this->session->get('user_identity')['name']) {
                continue;
            }
            $name[] = $item->getName();
        }
        return $this->response->setJsonContent($name);
    }

    public function subscribeListAction()
    {
        if ($this->request->isPost()) {

            $users = Subscription::find(
                [
                    'subscriber = :subscriber:',
                    'bind' => [
                        'subscriber' => $this->session->get('user_identity')['id']
                    ]
                ]
            );

            if ($users === false) {
                return $this->response->setJsonContent($users);
            }

            $subscribers = [];

            foreach ($users as $user) {
                if ($user->users->hasPhoto()) {
                    $src = $user->users->getPhoto();
                } else {
                    $gravatar = $this->getDi()->getShared('gravatar');
                    $src = $gravatar->getAvatar($user->users->getEmail());
                }

                $subscribers[] = [
                    'id' => $user->users->getId(),
                    'name' => $user->users->getName(),
                    'photo' => $src
                ];
            }

            return $this->response->setJsonContent(
                [
                    'users' => $subscribers
                ]
            );
        }
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