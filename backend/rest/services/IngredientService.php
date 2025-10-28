<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/IngredientDAO.php';

/**
 * IngredientService - Business logic for ingredient management
 * Handles ingredient operations including creation, updates, and usage tracking
 */
class IngredientService extends BaseService {
    
    public function __construct() {
        parent::__construct(new IngredientDAO());
    }
    
    /**
     * Get ingredients with usage count
     * @return array
     */
    public function getIngredientsWithUsage() {
        try {
            $ingredients = $this->dao->getAllWithUsageCount();
            return [
                'success' => true,
                'data' => $ingredients,
                'message' => 'Ingredients with usage count retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving ingredients: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get most used ingredients
     * @param int $limit
     * @return array
     */
    public function getMostUsedIngredients($limit = 10) {
        try {
            $ingredients = $this->dao->query(
                "SELECT i.*, COUNT(ri.recipe_id) as usage_count 
                 FROM ingredients i 
                 INNER JOIN recipe_ingredients ri ON i.id = ri.ingredient_id 
                 GROUP BY i.id 
                 ORDER BY usage_count DESC, i.name 
                 LIMIT :limit",
                ['limit' => $limit]
            );
            
            return [
                'success' => true,
                'data' => $ingredients,
                'message' => 'Most used ingredients retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving most used ingredients: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get ingredient by name
     * @param string $name
     * @return array
     */
    public function getByName($name) {
        try {
            if (empty($name)) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Ingredient name is required'
                ];
            }
            
            $ingredient = $this->dao->getByName($name);
            
            if ($ingredient) {
                return [
                    'success' => true,
                    'data' => $ingredient,
                    'message' => 'Ingredient found'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Ingredient not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving ingredient: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get recipes that use this ingredient
     * @param int $ingredientId
     * @return array
     */
    public function getRecipesUsingIngredient($ingredientId) {
        try {
            $recipes = $this->dao->query(
                "SELECT r.*, ri.quantity, ri.unit, ri.notes
                 FROM recipes r
                 INNER JOIN recipe_ingredients ri ON r.id = ri.recipe_id
                 WHERE ri.ingredient_id = :ingredient_id
                 ORDER BY r.title",
                ['ingredient_id' => $ingredientId]
            );
            
            return [
                'success' => true,
                'data' => $recipes,
                'message' => 'Recipes using ingredient retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving recipes: ' . $e->getMessage()
            ];
        }
    }
    
    // Required abstract method implementations
    protected function validateCreate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Ingredient name is required';
        } else {
            // Check if name already exists
            $existing = $this->dao->getByName($data['name']);
            if ($existing) {
                $errors[] = 'Ingredient name already exists';
            }
        }
        
        if (isset($data['name']) && strlen($data['name']) > 100) {
            $errors[] = 'Ingredient name must be 100 characters or less';
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
                $errors[] = 'Ingredient name cannot be empty';
            } else {
                // Check if name already exists for other ingredients
                $existing = $this->dao->getByName($data['name']);
                if ($existing && $existing['id'] != $id) {
                    $errors[] = 'Ingredient name already exists';
                }
            }
            
            if (strlen($data['name']) > 100) {
                $errors[] = 'Ingredient name must be 100 characters or less';
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
            $usageCount = $this->dao->query_unique(
                "SELECT COUNT(*) as count FROM recipe_ingredients WHERE ingredient_id = :id",
                ['id' => $id]
            );
            
            if ($usageCount['count'] > 0) {
                return [
                    'can_delete' => false,
                    'message' => 'Cannot delete ingredient used in recipes'
                ];
            }
            
            return [
                'can_delete' => true,
                'message' => 'Ingredient can be deleted'
            ];
        } catch (Exception $e) {
            return [
                'can_delete' => false,
                'message' => 'Error checking delete permissions'
            ];
        }
    }
}