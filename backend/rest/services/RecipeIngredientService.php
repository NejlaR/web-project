<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/RecipeIngredientDAO.php';
require_once __DIR__ . '/../dao/RecipeDAO.php';
require_once __DIR__ . '/../dao/IngredientDAO.php';

/**
 * RecipeIngredientService - Business logic for recipe-ingredient relationships
 * Handles recipe ingredient operations including adding, updating, and removing ingredients from recipes
 */
class RecipeIngredientService extends BaseService {
    
    private $recipeDAO;
    private $ingredientDAO;
    
    public function __construct() {
        parent::__construct(new RecipeIngredientDAO());
        $this->recipeDAO = new RecipeDAO();
        $this->ingredientDAO = new IngredientDAO();
    }
    
    /**
     * Get ingredients for a specific recipe
     * @param int $recipeId
     * @return array
     */
    public function getByRecipeId($recipeId) {
        try {
            $ingredients = $this->dao->getByRecipe($recipeId);
            
            return [
                'success' => true,
                'data' => $ingredients,
                'message' => 'Recipe ingredients retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving recipe ingredients: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get recipes that use a specific ingredient
     * @param int $ingredientId
     * @return array
     */
    public function getByIngredientId($ingredientId) {
        try {
            $recipes = $this->dao->getByIngredient($ingredientId);
            
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
    
    /**
     * Add multiple ingredients to a recipe
     * @param int $recipeId
     * @param array $ingredients
     * @return array
     */
    public function addIngredientsToRecipe($recipeId, $ingredients) {
        try {
            $results = [];
            $errors = [];
            
            foreach ($ingredients as $ingredient) {
                $ingredient['recipe_id'] = $recipeId;
                $result = $this->create($ingredient);
                
                if ($result['success']) {
                    $results[] = $result['data'];
                } else {
                    $errors[] = $result['message'];
                }
            }
            
            if (empty($errors)) {
                return [
                    'success' => true,
                    'data' => $results,
                    'message' => 'All ingredients added to recipe successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => $results,
                    'message' => 'Some ingredients failed to add: ' . implode(', ', $errors)
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error adding ingredients to recipe: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update recipe ingredient quantity/unit
     * @param int $recipeId
     * @param int $ingredientId
     * @param array $data
     * @return array
     */
    public function updateRecipeIngredient($recipeId, $ingredientId, $data) {
        try {
            $existing = $this->dao->query_unique(
                "SELECT * FROM recipe_ingredients WHERE recipe_id = :recipe_id AND ingredient_id = :ingredient_id",
                ['recipe_id' => $recipeId, 'ingredient_id' => $ingredientId]
            );
            
            if (!$existing) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Recipe ingredient relationship not found'
                ];
            }
            
            return $this->update($existing['id'], $data);
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error updating recipe ingredient: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Remove ingredient from recipe
     * @param int $recipeId
     * @param int $ingredientId
     * @return array
     */
    public function removeIngredientFromRecipe($recipeId, $ingredientId) {
        try {
            $existing = $this->dao->query_unique(
                "SELECT * FROM recipe_ingredients WHERE recipe_id = :recipe_id AND ingredient_id = :ingredient_id",
                ['recipe_id' => $recipeId, 'ingredient_id' => $ingredientId]
            );
            
            if (!$existing) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Recipe ingredient relationship not found'
                ];
            }
            
            return $this->delete($existing['id']);
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error removing ingredient from recipe: ' . $e->getMessage()
            ];
        }
    }
    
    // Required abstract method implementations
    protected function validateCreate($data) {
        $errors = [];
        
        // Validate required fields
        if (empty($data['recipe_id'])) {
            $errors[] = 'Recipe ID is required';
        } else {
            // Check if recipe exists
            $recipe = $this->recipeDAO->read($data['recipe_id']);
            if (!$recipe) {
                $errors[] = 'Invalid recipe ID';
            }
        }
        
        if (empty($data['ingredient_id'])) {
            $errors[] = 'Ingredient ID is required';
        } else {
            // Check if ingredient exists
            $ingredient = $this->ingredientDAO->read($data['ingredient_id']);
            if (!$ingredient) {
                $errors[] = 'Invalid ingredient ID';
            }
        }
        
        if (!isset($data['quantity']) || $data['quantity'] === '') {
            $errors[] = 'Quantity is required';
        } else {
            $quantity = floatval($data['quantity']);
            if ($quantity <= 0) {
                $errors[] = 'Quantity must be greater than 0';
            }
        }
        
        if (empty($data['unit'])) {
            $errors[] = 'Unit is required';
        } else {
            if (strlen($data['unit']) > 50) {
                $errors[] = 'Unit must be 50 characters or less';
            }
        }
        
        // Check for duplicate recipe-ingredient combination
        if (!empty($data['recipe_id']) && !empty($data['ingredient_id'])) {
            $existing = $this->dao->query_unique(
                "SELECT * FROM recipe_ingredients WHERE recipe_id = :recipe_id AND ingredient_id = :ingredient_id",
                ['recipe_id' => $data['recipe_id'], 'ingredient_id' => $data['ingredient_id']]
            );
            if ($existing) {
                $errors[] = 'This ingredient is already added to the recipe';
            }
        }
        
        // Validate optional fields
        if (isset($data['notes']) && strlen($data['notes']) > 500) {
            $errors[] = 'Notes must be 500 characters or less';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function validateUpdate($data, $id) {
        $errors = [];
        
        // Validate quantity if provided
        if (isset($data['quantity'])) {
            $quantity = floatval($data['quantity']);
            if ($quantity <= 0) {
                $errors[] = 'Quantity must be greater than 0';
            }
        }
        
        // Validate unit if provided
        if (isset($data['unit'])) {
            if (empty($data['unit'])) {
                $errors[] = 'Unit cannot be empty';
            } else {
                if (strlen($data['unit']) > 50) {
                    $errors[] = 'Unit must be 50 characters or less';
                }
            }
        }
        
        // Validate notes if provided
        if (isset($data['notes']) && strlen($data['notes']) > 500) {
            $errors[] = 'Notes must be 500 characters or less';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function processDataForCreate($data) {
        // Trim whitespace
        if (isset($data['unit'])) {
            $data['unit'] = trim($data['unit']);
        }
        if (isset($data['notes'])) {
            $data['notes'] = trim($data['notes']);
        }
        
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $data;
    }
    
    protected function processDataForUpdate($data, $id) {
        // Trim whitespace
        if (isset($data['unit'])) {
            $data['unit'] = trim($data['unit']);
        }
        if (isset($data['notes'])) {
            $data['notes'] = trim($data['notes']);
        }
        
        return $data;
    }
    
    protected function performSearch($query) {
        return $this->dao->search($query);
    }
    
    protected function canDelete($id) {
        // Recipe ingredients can always be deleted
        return [
            'can_delete' => true,
            'message' => 'Recipe ingredient can be deleted'
        ];
    }
}