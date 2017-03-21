<?php

namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;

/**
 * Class AbstractController
 * @package Application\Controller
 */
abstract class AbstractController extends AbstractActionController
{
    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getEvent()
            ->getApplication()
            ->getServiceManager()
            ->get('Doctrine\ORM\EntityManager');
    }

    /**
     * @return SessionManager
     */
    protected function getSession()
    {
        return $this->getEvent()
            ->getApplication()
            ->getServiceManager()
            ->get(SessionManager::class);
    }
}