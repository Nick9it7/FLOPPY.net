<?php

namespace Application\Controller;

use Application\Form\LoginForm;
use Application\Form\RegisterForm;
use Application\Model\User;
use Zend\Http\Request;
use Zend\Session\Container;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * Class UserController
 * @package Application\Controller
 */
class UserController extends AbstractController
{
    public function restrictLoggedIn()
    {
        if ($this->getSession()->sessionExists() === true) {
            $this->redirect()->toRoute('home');
        }
    }

    /**
     * Login action
     * @return ViewModel
     */
    public function loginAction()
    {
        $this->restrictLoggedIn();

        /** @var Request $request */
        $request = $this->getRequest();

        if (true === $request->isPost()) {
            $result = new JsonModel();
            $form = new LoginForm();

            $data = $request->getPost();

            if ($form->setData($data)->isValid() === true) {

                $user = $this->getEntityManager()->getRepository(User::class)->findBy([
                    'email' => $this->params()->fromPost('email'),
                    'password' => User::hashPassword($this->params()->fromPost('password'))
                ]);

                if (!empty($user)) {
                    $container = new Container('UserRegistration', $this->getSession());
                    $container->id = $user[0];
                    $result->setVariable('redirect',$this->url()->fromRoute('home'));
                } else {
                    $result->setVariable('errors', 'Email or password is incorrect. Try again');
                }

            } else {
                $result->setVariable('errors', $form->getMessages());
            }
            return $result;
        }
        return new ViewModel();
    }

    /**
     * @return ViewModel|array
     */
    public function registerAction()
    {
        $this->restrictLoggedIn();

        /** @var Request $request */
        $request = $this->getRequest();

        if (true === $request->isPost()) {
            $result = new JsonModel();
            $form = new RegisterForm();

            $data = $request->getPost();

            if ($form->setData($data)->isValid() === true) {

                $user = $this->getEntityManager()->getRepository(User::class)->findBy([
                    'email' => $this->params()->fromPost('email'),
                ]);

                if (!empty($user)) {
                    $user = new User();
                    $user->setEmail($form->get('email')->getValue());
                    $user->setName($form->get('name')->getValue());
                    $user->setPassword(User::hashPassword($form->get('password')->getValue()));
                    $user->setPhoto('');

                    $this->getEntityManager()->persist($user);
                    $this->getEntityManager()->flush();

                }                $this->getAuth()->getStorage()->write($user->getId());


            } else {
                $result->setVariable('errors', $form->getMessages());
            }
            return $result;
        }
    }

    /**
     * @return void
     */
    public function logoutAction()
    {
        $this->getAuth()
            ->getStorage()
            ->clear();

        $this->redirect()->toRoute('user', ['action' => 'login']);
    }
}