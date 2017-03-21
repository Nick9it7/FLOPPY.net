<?php

namespace Application\Controller;

use Application\Form\RegisterForm;
use Application\Model\User;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;

/**
 * Class UserController
 * @package Application\Controller
 */
class UserController extends AbstractController
{

    public function loginAction()
    {

        if ($this->getRequest()->isPost() === true) {

            $user = $this->getEntityManager()->getRepository(User::class)->findBy([
                'login' => $this->params()->fromPost('login'),
                'password' => $this->params()->fromPost('password')
            ]);

            if (!empty($user)) {
                $container = new Container('UserRegistration', $this->getSession());
                $container->id = $user[0];
            }
        }

        return new ViewModel([
            'repo' => $container->id
        ]);
    }

    public function registerAction()
    {

        if ($this->getRequest()->isPost === true) {

            $form = new RegisterForm();

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $user = new User();
                $user->setLogin();

            }


        }

        return new ViewModel();
    }

    public function logoutAction()
    {

        return new ViewModel();
    }
}