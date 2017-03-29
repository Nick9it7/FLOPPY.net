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

    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function registerAction()
    {

        if ($this->request->isPost()) {
            $form = new RegistrationForm();
            $error = [];

            if ($form->isValid($this->request->getPost())) {

                    /**
                     * @var Users
                     */
                    $user = new Users();
                    $user->setEmail($this->request->getPost('email'));
                    $user->setName($this->request->getPost('name'));
                    $user->setPassword(
                        $this->security->hash($this->request->getPost('password'))
                    );

                if ($user->save() === false) {
                    foreach ($user->getMessages() as $message)
                        $error[] = [
                            'field' => $message->getField(),
                            'message' => $message->getMessage()
                        ];
                } else {
                    $this->dispatcher->forward(
                        [
                            'action'     => 'login'
                        ]
                    );
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

    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
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

                    if ($this->security->checkHash($password, $user->getPassword()) === true) {
                        $this->session->set('user_identity',[
                            'id' => $user->getId()
                        ]);

                        $this->flashSession->success('Вітаємо ' . $user->getName());
                        return $this->response->setJsonContent(
                            [
                                'redirect' => 'index'
                            ]
                        );
                    }

                    $error[] = [
                        'field' => 'password',
                        'message' =>'Пароль невірний. Попробуйте ще раз'
                    ];

                } else {
                    $error[] = [
                        'field' => 'email',
                        'message' => 'Email введений невірно'
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

    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
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

                }

                $error[] = [
                    'field' => 'email',
                    'message' => 'Не знайдено користувача за таким email'
                ];

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

    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
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
                        'message' =>'Посилання для відновлення пароля є невірним'
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
                    $this->flashSession->success('Пароль змінено успішно');

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
                $this->dispatcher->forward(['controller' => 'error', 'action' =>   'notFound']);
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
                    $this->dispatcher->forward(['controller' => 'error', 'action' =>   'notFound']);
                }

                $this->view->hash = $hash;
            }
        }
    }

    public function recoverPasswordCancelAction()
    {
        $hash = trim($this->dispatcher->getParam('hash'), '/');

        if (true === empty($hash)) {
            $this->dispatcher->forward(['controller' => 'error', 'action' =>   'notFound']);
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
                $this->dispatcher->forward(['controller' => 'error', 'action' =>   'notFound']);
            } else {
                $recoverModel->setActive(false);
                $recoverModel->save();
                $this->flashSession->notice('Відновлення пароля перервано');
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