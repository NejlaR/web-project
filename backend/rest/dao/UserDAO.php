<?php

require_once 'BaseDAO.php';

/**
 * UserDAO - Data Access Object for users table
 * Handles CRUD operations for users
 */
class UserDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct('users');
    }
    
    /**
     * Create a new user
     * @param array $data
     * @return int|false - Returns user ID on success, false on failure
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing user
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUser($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get user by email
     * @param string $email
     * @return array|null
     */
    public function getByEmail($email) {
        return $this->query_unique("SELECT u.*, r.name as role_name 
                  FROM {$this->table_name} u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  WHERE u.email = :email", ['email' => $email]);
    }
    
    /**
     * Get user with role information
     * @param int $id
     * @return array|null
     */
    public function getByIdWithRole($id) {
        return $this->query_unique("SELECT u.*, r.name as role_name 
                  FROM {$this->table_name} u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  WHERE u.id = :id", ['id' => $id]);
    }
    
    /**
     * Get all users with role information
     * @return array
     */
    public function getAllWithRoles() {
        return $this->query("SELECT u.*, r.name as role_name 
                  FROM {$this->table_name} u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  ORDER BY u.created_at DESC", []);
    }
    
    /**
     * Check if email exists
     * @param string $email
     * @param int|null $excludeId - Exclude this user ID from check (for updates)
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT id FROM {$this->table_name} WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $result = $this->query($query, $params);
        return !empty($result);
    }
    
    /**
     * Get users by role
     * @param int $roleId
     * @return array
     */
    public function getByRole($roleId) {
        return $this->query("SELECT u.*, r.name as role_name 
                  FROM {$this->table_name} u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  WHERE u.role_id = :role_id 
                  ORDER BY u.name", ['role_id' => $roleId]);
    }
    
    /**
     * Search users by name or email
     * @param string $searchTerm
     * @return array
     */
    public function search($searchTerm) {
        $searchParam = '%' . $searchTerm . '%';
        return $this->query("SELECT u.*, r.name as role_name 
                  FROM {$this->table_name} u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  WHERE u.name LIKE :search OR u.email LIKE :search 
                  ORDER BY u.name", ['search' => $searchParam]);
    }
}