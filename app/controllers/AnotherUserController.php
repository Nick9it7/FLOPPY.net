<?php

use Phalcon\Mvc\Controller;

class AnotherUserController extends Controller
{
    public function initialize()
    {
        $this->view->setTemplateAfter("profile");
    }

    public function showAction()
    {
        if ($this->request->isPost()) {
            $namePattern = $this->request->getPost('name');

            /**
             * @var Users $user
             */
            $user = Users::findFirst(
                [
                    'name = :name:',
                    'bind' => [
                        'name' => $namePattern,
                    ]
                ]
            );

            if ($namePattern === $this->session->get('user_identity')['name']) {
                $user = false;
            }

            if ($user === false) {
                $this->dispatcher->forward(
                    [
                        'action' => 'search'
                    ]
                );
            } else {
                $this->view->user = [
                    'user' => $user
                ];
            }
        }
    }

    public function searchAction()
    {
        $users = Users::find();
        $matched = [];

        foreach ($users as $user) {
            $name = $user->getName();
            if ($name === $this->session->get('user_identity')['name']) {
                continue;
            }
            $res = stristr($name, $this->request->getPost('name'));

            if ($res !== false) {
                if ($user->hasPhoto()) {
                    $src = $user->getPhoto();
                } else {
                    $gravatar = $this->getDi()->getShared('gravatar');
                    $src = $gravatar->getAvatar($user->getEmail());
                }

                $matched[] = [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'photo' => $src
                ];
            }
        }

        if (!empty($matched)) {
            $this->view->users = $matched;
        }
    }

    public function subscribeAction()
    {
        if ($this->request->isPost()) {

            $subscription = new Subscription();
            $subscription->setSubscriber($this->session->get('user_identity')['id']);
            $subscription->setUser($this->request->getPost('subscriber'));
            $res = $subscription->save();
            return $this->response->setJsonContent(
                [
                    'subscribe' => $res
                ]
            );
        }
    }

    public function unsubscribeAction()
    {
        if ($this->request->isPost()) {
            $subscription = Subscription::findFirst(
                [
                    'subscriber = :subscriber: AND user = :user:',
                    'bind' => [
                        'subscriber' => $this->session->get('user_identity')['id'],
                        'user' => $this->request->getPost('subscriber')
                    ]
                ]
            );
            $res = $subscription->delete();
            return $this->response->setJsonContent(
                [
                    'unsubscribe' => $res
                ]
            );
        }
    }
}