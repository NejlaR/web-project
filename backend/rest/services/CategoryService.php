<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/CategoryDAO.php';

/**
 * CategoryService - Business logic for category management
 * Handles category operations including creation, updates, and validation
 */
class CategoryService extends BaseService {
    
    public function __construct() {
        parent::__construct(new CategoryDAO());
    }
    
    /**
     * Get categories with recipe count
     * @return array
     */
    public function getCategoriesWithRecipeCount() {
        try {
            $categories = $this->dao->getAllWithRecipeCount();
            return [
                'success' => true,
                'data' => $categories,
                'message' => 'Categories with recipe count retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving categories: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get category by name
     * @param string $name
     * @return array
     */
    public function getByName($name) {
        try {
            if (empty($name)) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Category name is required'
                ];
            }
            
            $category = $this->dao->getByName($name);
            
            if ($category) {
                return [
                    'success' => true,
                    'data' => $category,
                    'message' => 'Category found'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Category not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving category: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get popular categories (with most recipes)
     * @param int $limit
     * @return array
     */
    public function getPopularCategories($limit = 5) {
        try {
            $categories = $this->dao->query(
                "SELECT c.*, COUNT(r.id) as recipe_count 
                 FROM categories c 
                 LEFT JOIN recipes r ON c.id = r.category_id 
                 GROUP BY c.id 
                 ORDER BY recipe_count DESC, c.name 
                 LIMIT :limit",
                ['limit' => $limit]
            );
            
            return [
                'success' => true,
                'data' => $categories,
                'message' => 'Popular categories retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving popular categories: ' . $e->getMessage()
            ];
        }
    }
    
    // Required abstract method implementations
    protected function validateCreate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Category name is required';
        } else {
            // Check if name already exists
            $existing = $this->dao->getByName($data['name']);
            if ($existing) {
                $errors[] = 'Category name already exists';
            }
        }
        
        if (isset($data['name']) && strlen($data['name']) > 100) {
            $errors[] = 'Category name must be 100 characters or less';
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
                $errors[] = 'Category name cannot be empty';
            } else {
                // Check if name already exists for other categories
                $existing = $this->dao->getByName($data['name']);
                if ($existing && $existing['id'] != $id) {
                    $errors[] = 'Category name already exists';
                }
            }
            
            if (strlen($data['name']) > 100) {
                $errors[] = 'Category name must be 100 characters or less';
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
        if (isset($data['description'])) {
            $data['description'] = trim($data['description']);
        }
        
        return $data;
    }
    
    protected function processDataForUpdate($data, $id) {
        // Trim whitespace
        if (isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }
        if (isset($data['description'])) {
            $data['description'] = trim($data['description']);
        }
        
        return $data;
    }
    
    protected function performSearch($query) {
        return $this->dao->search($query);
    }
    
    protected function canDelete($id) {
        try {
            $canDelete = $this->dao->canDelete($id);
            
            if ($canDelete) {
                return [
                    'can_delete' => true,
                    'message' => 'Category can be deleted'
                ];
            } else {
                return [
                    'can_delete' => false,
                    'message' => 'Cannot delete category with associated recipes'
                ];
            }
        } catch (Exception $e) {
            return [
                'can_delete' => false,
                'message' => 'Error checking delete permissions'
            ];
        }
    }
}