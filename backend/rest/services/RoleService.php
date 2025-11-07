<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/RoleDAO.php';

/**
 * RoleService - Business logic for role management
 * Handles role operations including creation, updates, and user assignments
 */
class RoleService extends BaseService {
    
    public function __construct() {
        parent::__construct(new RoleDAO());
    }
    
    /**
     * Get role by name
     * @param string $name
     * @return array
     */
    public function getByName($name) {
        try {
            if (empty($name)) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Role name is required'
                ];
            }
            
            $role = $this->dao->getByName($name);
            
            if ($role) {
                return [
                    'success' => true,
                    'data' => $role,
                    'message' => 'Role found'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Role not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving role: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get users by role
     * @param int $roleId
     * @return array
     */
    public function getUsersByRole($roleId) {
        try {
            $users = $this->dao->query(
                "SELECT u.* FROM users u WHERE u.role_id = :role_id ORDER BY u.name",
                ['role_id' => $roleId]
            );
            
            return [
                'success' => true,
                'data' => $users,
                'message' => 'Users for role retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving users: ' . $e->getMessage()
            ];
        }
    }
    
    // Required abstract method implementations
    protected function validateCreate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Role name is required';
        } else {
            // Check if name already exists
            $existing = $this->dao->getByName($data['name']);
            if ($existing) {
                $errors[] = 'Role name already exists';
            }
        }
        
        if (isset($data['name']) && strlen($data['name']) > 50) {
            $errors[] = 'Role name must be 50 characters or less';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function validateUpdate($data, $id) {
        $errors = [];
        
        if (isset($data['name'])) {
            if (empty($data['name'])) {
                $errors[] = 'Role name cannot be empty';
            } else {
                // Check if name already exists for other roles
                $existing = $this->dao->getByName($data['name']);
                if ($existing && $existing['id'] != $id) {
                    $errors[] = 'Role name already exists';
                }
            }
            
            if (strlen($data['name']) > 50) {
                $errors[] = 'Role name must be 50 characters or less';
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function processDataForCreate($data) {
        // Trim whitespace
        if (isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }
        
        return $data;
    }
    
    protected function processDataForUpdate($data, $id) {
        // Trim whitespace
        if (isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }
        
        return $data;
    }
    
    protected function performSearch($query) {
        return $this->dao->search($query);
    }
    
    protected function canDelete($id) {
        try {
            $userCount = $this->dao->query_unique(
                "SELECT COUNT(*) as count FROM users WHERE role_id = :id",
                ['id' => $id]
            );
            
            if ($userCount['count'] > 0) {
                return [
                    'can_delete' => false,
                    'message' => 'Cannot delete role assigned to users'
                ];
            }
            
            return [
                'can_delete' => true,
                'message' => 'Role can be deleted'
            ];
        } catch (Exception $e) {
            return [
                'can_delete' => false,
                'message' => 'Error checking delete permissions'
            ];
        }
    }
}