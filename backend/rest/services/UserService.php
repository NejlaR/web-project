<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/UserDAO.php';

/**
 * UserService - Business logic for user management
 * Handles user operations including registration, authentication, and profile management
 */
class UserService extends BaseService {
    
    public function __construct() {
        parent::__construct(new UserDAO());
    }
    
    /**
     * Register new user
     * @param array $data
     * @return array
     */
    public function register($data) {
        try {
            // Check if email already exists
            if (isset($data['email'])) {
                $existingUser = $this->dao->getByEmail($data['email']);
                if ($existingUser) {
                    return [
                        'success' => false,
                        'data' => null,
                        'message' => 'Email already exists'
                    ];
                }
            }
            
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                unset($data['password']); // Remove plain password
            }
            
            // Set default role if not provided
            if (!isset($data['role_id'])) {
                $data['role_id'] = 3; // Default to 'user' role
            }
            
            return $this->create($data);
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Registration error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Authenticate user login
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login($email, $password) {
        try {
            if (empty($email) || empty($password)) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email and password are required'
                ];
            }
            
            $user = $this->dao->getByEmail($email);
            
            if (!$user) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Invalid credentials'
                ];
            }
            
            if (password_verify($password, $user['password_hash'])) {
                // Remove password hash from response
                unset($user['password_hash']);
                
                return [
                    'success' => true,
                    'data' => $user,
                    'message' => 'Login successful'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Invalid credentials'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Login error: ' . $e->getMessage()
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
            $users = $this->dao->getByRole($roleId);
            return [
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving users: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Change user password
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return array
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            $user = $this->getById($userId);
            if (!$user['success']) {
                return $user;
            }
            
            // Get user with password hash
            $userWithPassword = $this->dao->query_unique(
                "SELECT password_hash FROM users WHERE id = :id",
                ['id' => $userId]
            );
            
            if (!password_verify($currentPassword, $userWithPassword['password_hash'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Current password is incorrect'
                ];
            }
            
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            return $this->update($userId, ['password_hash' => $newPasswordHash]);
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error changing password: ' . $e->getMessage()
            ];
        }
    }
    
    // Required abstract method implementations
    protected function validateCreate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($data['password']) && empty($data['password_hash'])) {
            $errors[] = 'Password is required';
        }
        
        if (!empty($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function validateUpdate($data, $id) {
        $errors = [];
        
        if (isset($data['name']) && empty($data['name'])) {
            $errors[] = 'Name cannot be empty';
        }
        
        if (isset($data['email'])) {
            if (empty($data['email'])) {
                $errors[] = 'Email cannot be empty';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } else {
                // Check if email exists for other users
                $existingUser = $this->dao->getByEmail($data['email']);
                if ($existingUser && $existingUser['id'] != $id) {
                    $errors[] = 'Email already exists';
                }
            }
        }
        
        if (isset($data['password']) && strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function processDataForCreate($data) {
        // Hash password if not already hashed
        if (isset($data['password']) && !isset($data['password_hash'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return $data;
    }
    
    protected function processDataForUpdate($data, $id) {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        return $data;
    }
    
    protected function performSearch($query) {
        return $this->dao->search($query);
    }
    
    protected function canDelete($id) {
        // Check if user has any associated recipes or reviews
        try {
            $recipeCount = $this->dao->query_unique(
                "SELECT COUNT(*) as count FROM recipes WHERE user_id = :id",
                ['id' => $id]
            );
            
            $reviewCount = $this->dao->query_unique(
                "SELECT COUNT(*) as count FROM reviews WHERE user_id = :id",
                ['id' => $id]
            );
            
            if ($recipeCount['count'] > 0 || $reviewCount['count'] > 0) {
                return [
                    'can_delete' => false,
                    'message' => 'Cannot delete user with associated recipes or reviews'
                ];
            }
            
            return [
                'can_delete' => true,
                'message' => 'User can be deleted'
            ];
        } catch (Exception $e) {
            return [
                'can_delete' => false,
                'message' => 'Error checking delete permissions'
            ];
        }
    }
}