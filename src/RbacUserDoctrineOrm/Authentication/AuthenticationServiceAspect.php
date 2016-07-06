<?php
namespace RbacUserDoctrineOrm\Authentication;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Adapter\AdapterInterface;
use Doctrine\ORM\EntityManagerInterface;
use ZfcUser\Options\ModuleOptions;

class AuthenticationServiceAspect implements AuthenticationServiceInterface
{
    
    /**
     * 
     * @var AuthenticationServiceInterface
     */
    private $wrappedAuthenticationService;
    
    /**
     *
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * 
     * @var ModuleOptions
     */
    private $zfcuserModuleOptions;
    
    private $managedIdentity;
    
    public function __construct(AuthenticationServiceInterface $wrappedAuthenticationService, EntityManagerInterface $em, ModuleOptions $zfcuserModuleOptions)
    {
        $this->zfcuserModuleOptions = $zfcuserModuleOptions;
        $this->wrappedAuthenticationService = $wrappedAuthenticationService;
        $this->em = $em;
    }

    public function authenticate(AdapterInterface $adapter = null)
    {
        return  $this->wrappedAuthenticationService->authenticate($adapter);
    }

    public function hasIdentity()
    {
        return  $this->wrappedAuthenticationService->hasIdentity();
    }

    public function getIdentity()
    {
        if ( null === $this->managedIdentity){
            if( $this->hasIdentity() ){
                $this->managedIdentity = $this->em->find($this->zfcuserModuleOptions->getUserEntityClass(), $this->wrappedAuthenticationService->getIdentity()->getId());
            }else{
                return null;
            }
        }
        return $this->managedIdentity;
    }

    public function clearIdentity()
    {
        return  $this->wrappedAuthenticationService->clearIdentity();
    }
}