<?php

namespace Anax\Users;

/**
 * Model for user roles.
 *
 */
class UserRole extends \Anax\MVC\CDatabaseModel
{

    private function getRoles($userId)
    {
        $this->db->select()->from('UserRole')->where('userId = ?');
        $this->db->execute([$userId]);
        return $this->db->fetchAll();
    }

    public function assign($userId, $role)
    {
        $this->db->insert(
            'UserRole',
            ['userId', 'userRole']
        );
        $this->db->execute([$userId, $role]);
    }

    public function getRolesOfUser($userId)
    {
        $roles = array();
        $objects = $this->getRoles($userId);
        foreach ($objects as $object) {
            array_push($roles, $object->userRole);
        }
        return $roles;
    }

    public function doesUserHaveRole($userId, $role)
    {
        $roles = $this->getRolesOfUser($userId);
        if (in_array($role, $roles)) {
            return true;
        } else {
            return false;
        }
    }
} 
