<?php

namespace RbacUserDoctrineOrm;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\DependencyIndicatorInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;

class Module
    implements BootstrapListenerInterface,
               ConfigProviderInterface,
               ServiceProviderInterface
{

    /**
     * Listen to the bootstrap event
     *
     * @param MvcEvent|EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $app     = $e->getParam('application');
        $sm      = $app->getServiceManager();
        $config  = $sm->get('Config');
        $chain = $sm->get('doctrine.driver.orm_default');
        
        if($config['rbac-user-doctrine-orm']['use_default_user']){
            $chain->addDriver(new XmlDriver(__DIR__ . '/../../config/xml/doctrine/user'), 'RbacUserDoctrineOrm\Entity\ConcreteUser');
        }
        if($config['rbac-user-doctrine-orm']['use_default_role']){
            $chain->addDriver(new XmlDriver(__DIR__ . '/../../config/xml/doctrine/role'), 'RbacUserDoctrineOrm\Entity\ConcreteRole');
        }
        if($config['rbac-user-doctrine-orm']['use_default_permission']){
            $chain->addDriver(new XmlDriver(__DIR__ . '/../../config/xml/doctrine/permission'), 'RbacUserDoctrineOrm\Entity\ConcretePermission');
        }
    }


    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     *
     * Set autoloader config for RbacUserDoctrineOrm module
     *
     * @return array\Traversable
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
    
    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Authentication\AuthenticationService' => function($sm) {
                    $zfcuserAuthService = $sm->get('zfcuser_auth_service');
                    $em = $sm->get('Doctrine\ORM\EntityManager');
                    $mo = $sm->get('zfcuser_module_options');
                    $authService = new \RbacUserDoctrineOrm\Authentication\AuthenticationServiceAspect($zfcuserAuthService, $em, $mo);
                    return $authService;
                },
                'ZfcUerDoctrineOrm\RoleRepository' => function ($sm){
                    $config = $sm->get('Config');
                    $roleEntityClass = $config['rbac-user-doctrine-orm']['role_entity_class'];
                    $em = $sm->get('zfcuser_doctrine_em');
                    return $em->getRepository($roleEntityClass);
                },
            ),
        );
    }

}
