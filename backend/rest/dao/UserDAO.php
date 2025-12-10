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
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing user
     */
    public function updateUser($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        return $this->query_unique("
            SELECT u.*, r.name AS role_name
            FROM {$this->table_name} u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email
        ", ['email' => $email]);
    }
    
    /**
     * Get user with role information by ID
     */
    public function getByIdWithRole($id) {
        return $this->query_unique("
            SELECT u.*, r.name AS role_name
            FROM {$this->table_name} u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.user_id = :id
        ", ['id' => $id]);
    }
    
    /**
     * Get all users with role information
     */
    public function getAllWithRoles() {
        return $this->query("
            SELECT u.*, r.name AS role_name
            FROM {$this->table_name} u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.user_id DESC
        ", []);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT user_id FROM {$this->table_name} WHERE email = :email";
        $params = ['email' => $email];

        if ($excludeId) {
            $query .= " AND user_id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->query($query, $params);
        return !empty($result);
    }
    
    /**
     * Get users by role ID
     */
    public function getByRole($roleId) {
        return $this->query("
            SELECT u.*, r.name AS role_name
            FROM {$this->table_name} u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.role_id = :role_id
            ORDER BY u.name
        ", ['role_id' => $roleId]);
    }
    
    /**
     * Search users by name or email
     */
    public function search($searchTerm) {
        $searchParam = '%' . $searchTerm . '%';

        return $this->query("
            SELECT u.*, r.name AS role_name
            FROM {$this->table_name} u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.name LIKE :search OR u.email LIKE :search
            ORDER BY u.name
        ", ['search' => $searchParam]);
    }
}
