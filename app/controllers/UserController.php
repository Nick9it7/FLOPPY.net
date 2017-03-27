<?php

use Phalcon\Mvc\Controller;

/**
 * Class UserController
 * @property \Phalcon\Ext\Mailer\Manager mailer
 */
class UserController extends Controller
{

    /**
     * Function that called before run action
     */
    public function beforeExecuteRoute()
    {
        if ($this->session->has('user_identity')) {
            $this->response->redirect('');
        }
    }

    public function indexAction()
    {
        $this->response->redirect('user/login');
    }

    public function registerAction()
    {

        if ($this->request->isPost()) {
            $form = new RegistrationForm();
            $error = [];

            if ($form->isValid($this->request->getPost())) {
                $email = Users::findFirst(
                    [
                        'email = :email:',
                        'bind' => [
                            'email'    => $this->request->getPost('email')
                        ]
                    ]
                );
                
                if ($email === false) {

                    /**
                     * @var Users
                     */
                    $user = new Users();
                    $user->setEmail($this->request->getPost('email'));
                    $user->setName($this->request->getPost('name'));
                    $user->setPassword(
                        $this->security->hash($this->request->getPost('password'))
                    );
                    $user->save();
                    $this->dispatcher->forward(
                        [
                            'action'     => 'login'
                        ]
                    );
                } else {
                    $error[] = [
                        'field' => 'email',
                        'message' => 'Email is exists'
                    ];
                }

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

    public function loginAction()
    {

        if ($this->request->isPost()) {
            $form = new LoginForm();
            $error = [];

            if ($form->isValid($this->request->getPost())) {

                /**
                 * @var Users $user
                 */
                $user = Users::findFirst(
                    [
                        'email = :email:',
                        'bind' => [
                            'email' => $this->request->getPost('email'),
                        ]
                    ]
                );

                if ($user !== false) {
                    $password = $this->request->getPost('password');

                    if ($this->security->checkHash($password, $user->getPassword())) {
                        $this->session->set('user_identity',[
                            'id' => $user->getId()
                        ]);

                        $this->flashSession->success('Welcome ' . $user->getName());
                        return $this->response->setJsonContent(
                            [
                                'redirect' => 'index'
                            ]
                        );

                    } else {
                        $error[] = [
                            'field' => 'password',
                            'message' =>'Password is incorrect. Try again'
                        ];
                    }

                } else {
                    $error[] = [
                        'field' => 'email',
                        'message' => 'User is not found'
                    ];
                }

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

    public function logoutAction()
    {
        $this->session->destroy('user_identity');
        $this->response->redirect('');
    }

    public function forgotPasswordAction()
    {
        if ($this->request->isPost()) {
            $form = new ForgotPasswordForm();
            $error = [];

            if ($form->isValid($this->request->getPost())) {

                /**
                 * @var Users $user
                 */
                $user = Users::findFirst(
                    [
                        'email = :email:',
                        'bind' => [
                            'email' => $this->request->getPost('email'),
                        ]
                    ]
                );

                if ($user !== false) {

                    /**
                     * @var PasswordRecovery $recovery
                     */
                    $recovery = new PasswordRecovery();
                    $recovery->setUser($user->getId());
                    $recovery->setHash(PasswordRecovery::generateHash());
                    $recovery->setActive(true);
                    $recovery->save();

                    $url = 'phalconproject/recoverPassword/' . $recovery->getHash();
                    $letter = 'To recover password go to ' . $this->tag->linkTo($url, 'url') . $this->tag->tagHtml('br');
                    $url = 'phalconproject/recoverPasswordCancel/' . $recovery->getHash();
                    $letter .= 'If you did not request a password recovery follow ' . $this->tag->linkTo($url, 'url');

                    $message = $this->mailer->createMessage()
                        ->to($user->getEmail())
                        ->subject('Recovery password')
                        ->content($letter);

                    $message->send();
                    $this->flashSession->notice('Check your email');
                    return $this->response->setJsonContent([
                        'redirect' => '/index'
                    ]);

                } else {
                    $error[] = [
                        'field' => 'email',
                        'message' => 'Email is not found'
                    ];
                }

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

    public function recoverPasswordAction()
    {
        if ($this->request->isPost()) {
            $form = new RecoverPasswordForm();
            $error = [];

            if ($form->isValid($this->request->getPost())) {

                /** @var PasswordRecovery $recoverModel */
                $recoverModel = PasswordRecovery::findFirst(
                    [
                        'hash = :hash: AND active = :active:',
                        'bind' => [
                            'hash' => $this->request->getPost('hash'),
                            'active' => true,
                        ]
                    ]
                );

                if ($recoverModel === false) {
                    $error[] = [
                        'field' => 'password',
                        'message' =>'Recovery password link is not valid'
                    ];
                } else {
                    $recoverModel->setActive(false);
                    $recoverModel->save();

                    /**
                     * @var Users $user
                     */
                    $user = $recoverModel->users;
                    $user->setPassword(
                        $this->security->hash($this->request->getPost('password'))
                    );
                    $user->save();
                    $this->flashSession->success('Password was changed successfully');

                    return $this->response->setJsonContent([
                        'redirect' => '/user/login'
                    ]);
                }
            } else {
                foreach ($form->getMessages() as $message)
                    $error[] = [
                        'field' => $message->getField(),
                        'message' => $message->getMessage()
                    ];

                $this->view->hash = $this->request->getPost('hash');
                $this->response->setJsonContent([
                    'error' => $error
                ]);
                return $this->response;
            }

        } else {
            $hash = trim($this->dispatcher->getParam('hash'), '/');

            if (true === empty($hash)) {
                $this->flashSession->error('Not found page');
                $this->dispatcher->forward(
                    [
                        'controller' => 'index',
                        'action' => 'index'
                    ]
                );
            } else {
                /** @var PasswordRecovery $recoverModel */
                $recoverModel = PasswordRecovery::findFirst(
                    [
                        'hash = :hash: AND active = :active:',
                        'bind' => [
                            'hash' => $hash,
                            'active' => true,
                        ]
                    ]
                );

                if ($recoverModel === false) {
                    $this->flashSession->error('Not found page');
                    $this->dispatcher->forward(
                        [
                            'controller' => 'index',
                            'action' => 'index'
                        ]
                    );
                }

                $this->view->hash = $hash;
            }
        }
    }

    public function recoverPasswordCancelAction()
    {
        $hash = trim($this->dispatcher->getParam('hash'), '/');

        if (true === empty($hash)) {
            $this->flashSession->error('Not found page');
            $this->dispatcher->forward(
                [
                    'controller' => 'index',
                    'action' => 'index'
                ]
            );
        } else {
            /** @var PasswordRecovery $recoverModel */
            $recoverModel = PasswordRecovery::findFirst(
                [
                    'hash = :hash: AND active = :active:',
                    'bind' => [
                        'hash' => trim($this->dispatcher->getParam('hash'), '/'),
                        'active' => true,
                    ]
                ]
            );

            if ($recoverModel === false) {
                $this->flashSession->error('Not found page');
                $this->dispatcher->forward(
                    [
                        'controller' => 'index',
                        'action' => 'index'
                    ]
                );
            } else {
                $recoverModel->setActive(false);
                $recoverModel->save();
                $this->flashSession->notice('Recovery password was aborted');
                $this->dispatcher->forward(
                    [
                        'controller' => 'user',
                        'action' => 'login'
                    ]
                );
            }
        }
    }
}