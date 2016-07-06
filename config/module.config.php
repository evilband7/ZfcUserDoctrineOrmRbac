<?php
return array(
    'rbac-user-doctrine-orm' => array(
        
        'use_default_user' => true,
        'use_default_role' => true,
        'use_default_permission' => true,
        'role_entity_class' => 'RbacUserDoctrineOrm\Entity\ConcreteRole\Role' // to be roleRepository.
        
    ),
    
    
    'zfc_rbac' => [
        'role_provider' => [
            'ZfcRbac\Role\ObjectRepositoryRoleProvider' => [
                'object_repository'  => 'ZfcUerDoctrineOrm\RoleRepository',
                'role_name_property' => 'name'
            ]
        ]
    ],
    
    'zfcuser' => array(
        'enable_default_entities' => false,
        'userEntityClass' => 'RbacUserDoctrineOrm\Entity\ConcreteUser\User'
    ),
    
    'doctrine' => array(
        'driver' => array(
            'RbacUserDoctrineEntityMappedSuperclass' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'paths' => __DIR__ . '/xml/doctrine/mappedsuperclass'
            ),

            'orm_default' => array(
                'drivers' => array(
                    'RbacUserDoctrineOrm\Entity\MappedSuperclass'  => 'RbacUserDoctrineEntityMappedSuperclass'
                )
            )
        )
    ),
    'view_manager' => array(
        'template_map' => array(
            'error/403' => __DIR__ . '/../view/error/403.phtml',
        )
    ),
);