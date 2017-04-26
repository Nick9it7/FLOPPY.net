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

            if ($this->request->getPost('name') === $this->session->get('user_identity')['name']) {
                $user = false;
            }

            if ($user === false) {
                $users = Users::find();
                $matched = [];

                foreach ($users as $user) {
                    $name = $user->getName();
                    $res = stristr($name, $namePattern);

                    if ($res !== false) {
                        $matched[] = $user;
                    }
                }

                if (empty($matched)) {
                    $this->dispatcher->forward(['controller' => 'error', 'action' =>   'notFound']);
                } else {
                   //---------------------
                    $this->view->render('index', 'search' );
                    $this->view->user = $matched[0];
                }
            } else {
                $this->view->user = $user;
            }

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