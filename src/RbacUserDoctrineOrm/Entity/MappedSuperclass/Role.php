<?php
/**
 * Role.php file
 *
 * PHP Version 5
 *
 * @category   ${category}
 * @package    Inventis
 * @subpackage Bricks
 * @author     Inventis Web Architects <info@inventis.be>
 * @license    Copyright © Inventis BVBA  - All rights reserved
 * @link       https://github.com/Inventis/Bricks
 */


namespace RbacUserDoctrineOrm\Entity\MappedSuperclass;


use Doctrine\ORM\PersistentCollection;
use RecursiveIterator;
use IteratorIterator;
use Rbac\Role\RoleInterface;


class Role implements RoleInterface{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Role
     */
    protected $parent;

    /**
     * @var PersistentCollection|Role[]
     */
    protected $children;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var PersistentCollection
     */
    protected $permissions;


    /**
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Role $parent
     * @return self
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param PersistentCollection $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return Role[]|RecursiveIterator[]
     */
    public function getChildren()
    {
        $children = $this->children->getValues();
        return $children[$this->childrenPosition];
    }

    /**
     * @param PersistentCollection $permissions
     * @return self
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
        return $this;
    }

    /**
     * @param bool $recursive when true child permissions of a role are returned as well
     * @return PersistentCollection|Permission[]
     */
    public function getPermissions($recursive=false)
    {
        if (!$recursive) {
            return $this->permissions;
        }
        $permissions =  $this->permissions->getValues();
        if($this->children){
            foreach ($this->children as $leaf) {
                $permissions = array_merge($permissions, $leaf->getPermissions(true));
            }
        }
        return $permissions;
    }


    /**
     * Checks if a permission exists for this role or any child roles.
     *
     * @param  string $name
     * @return bool
     */
    public function hasPermission($name)
    {
        foreach ($this->getPermissions(true) as $permission) {
            if ($permission->getName() == $name) {
                return true;
            }
        }

        return false;
    }

    public function __toString()
    {
        return $this->name;
    }
}
