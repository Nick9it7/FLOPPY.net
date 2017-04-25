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

    public function subscripeAction()
    {
        if ($this->request->isPost()) {

            $subscription = new Subscription();
            $subscription->setSubscriber($this->session->get('user_identity')['id']);
            $subscription->setUser($this->request->getPost('subscriber'));
            $res = $subscription->save();
            return $this->response->setJsonContent(
                [
                    'subscripe' => $res
                ]
            );
        }
    }

    public function unsubscripeAction()
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
                    'unsubscripe' => $res
                ]
            );
        }
    }
}