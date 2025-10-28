<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/RecipeDAO.php';
require_once __DIR__ . '/../dao/RecipeIngredientDAO.php';

/**
 * RecipeService - Business logic for recipe management
 * Handles recipe operations including creation, updates, ingredients, and advanced features
 */
class RecipeService extends BaseService {
    
    private $recipeIngredientDAO;
    
    public function __construct() {
        parent::__construct(new RecipeDAO());
        $this->recipeIngredientDAO = new RecipeIngredientDAO();
    }
    
    /**
     * Get recipe with full details including ingredients
     * @param int $id
     * @return array
     */
    public function getRecipeWithDetails($id) {
        try {
            $recipe = $this->dao->getByIdWithDetails($id);
            
            if (!$recipe) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Recipe not found'
                ];
            }
            
            // Get recipe ingredients
            $ingredients = $this->recipeIngredientDAO->getByRecipe($id);
            $recipe['ingredients'] = $ingredients;
            
            return [
                'success' => true,
                'data' => $recipe,
                'message' => 'Recipe retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving recipe: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Create recipe with ingredients
     * @param array $recipeData
     * @param array $ingredients
     * @return array
     */
    public function createRecipeWithIngredients($recipeData, $ingredients = []) {
        try {
            // Create the recipe first
            $recipeResult = $this->create($recipeData);
            
            if (!$recipeResult['success']) {
                return $recipeResult;
            }
            
            $recipeId = $recipeResult['data']['id'];
            
            // Add ingredients if provided
            if (!empty($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    $ingredient['recipe_id'] = $recipeId;
                    $this->recipeIngredientDAO->create($ingredient);
                }
            }
            
            // Return complete recipe with ingredients
            return $this->getRecipeWithDetails($recipeId);
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error creating recipe: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get recipes by user
     * @param int $userId
     * @return array
     */
    public function getRecipesByUser($userId) {
        try {
            $recipes = $this->dao->getByUser($userId);
            return [
                'success' => true,
                'data' => $recipes,
                'message' => 'User recipes retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving user recipes: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get recipes by category
     * @param int $categoryId
     * @return array
     */
    public function getRecipesByCategory($categoryId) {
        try {
            $recipes = $this->dao->query(
                "SELECT r.*, u.name as user_name,
                        AVG(rev.rating) as avg_rating, COUNT(rev.id) as review_count
                 FROM recipes r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 LEFT JOIN reviews rev ON r.id = rev.recipe_id
                 WHERE r.category_id = :category_id 
                 GROUP BY r.id 
                 ORDER BY r.created_at DESC",
                ['category_id' => $categoryId]
            );
            
            return [
                'success' => true,
                'data' => $recipes,
                'message' => 'Category recipes retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving category recipes: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get top rated recipes
     * @param int $limit
     * @return array
     */
    public function getTopRatedRecipes($limit = 10) {
        try {
            $recipes = $this->dao->query(
                "SELECT r.*, u.name as user_name, c.name as category_name,
                        AVG(rev.rating) as avg_rating, COUNT(rev.id) as review_count
                 FROM recipes r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 LEFT JOIN categories c ON r.category_id = c.id 
                 LEFT JOIN reviews rev ON r.id = rev.recipe_id
                 GROUP BY r.id 
                 HAVING avg_rating IS NOT NULL
                 ORDER BY avg_rating DESC, review_count DESC 
                 LIMIT :limit",
                ['limit' => $limit]
            );
            
            return [
                'success' => true,
                'data' => $recipes,
                'message' => 'Top rated recipes retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving top rated recipes: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get recent recipes
     * @param int $limit
     * @return array
     */
    public function getRecentRecipes($limit = 10) {
        try {
            $recipes = $this->dao->query(
                "SELECT r.*, u.name as user_name, c.name as category_name
                 FROM recipes r 
                 LEFT JOIN users u ON r.user_id = u.id 
                 LEFT JOIN categories c ON r.category_id = c.id 
                 ORDER BY r.created_at DESC 
                 LIMIT :limit",
                ['limit' => $limit]
            );
            
            return [
                'success' => true,
                'data' => $recipes,
                'message' => 'Recent recipes retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving recent recipes: ' . $e->getMessage()
            ];
        }
    }
    
    // Required abstract method implementations
    protected function validateCreate($data) {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = 'Recipe title is required';
        }
        
        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            $errors[] = 'Valid user ID is required';
        }
        
        if (empty($data['category_id']) || !is_numeric($data['category_id'])) {
            $errors[] = 'Valid category ID is required';
        }
        
        if (isset($data['prep_minutes']) && (!is_numeric($data['prep_minutes']) || $data['prep_minutes'] < 0)) {
            $errors[] = 'Prep minutes must be a positive number';
        }
        
        if (isset($data['cook_minutes']) && (!is_numeric($data['cook_minutes']) || $data['cook_minutes'] < 0)) {
            $errors[] = 'Cook minutes must be a positive number';
        }
        
        if (isset($data['servings']) && (!is_numeric($data['servings']) || $data['servings'] < 1)) {
            $errors[] = 'Servings must be at least 1';
        }
        
        if (isset($data['difficulty_level']) && !in_array($data['difficulty_level'], ['Easy', 'Medium', 'Hard'])) {
            $errors[] = 'Difficulty level must be Easy, Medium, or Hard';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function validateUpdate($data, $id) {
        $errors = [];
        
        if (isset($data['title']) && empty($data['title'])) {
            $errors[] = 'Recipe title cannot be empty';
        }
        
        if (isset($data['user_id']) && (!is_numeric($data['user_id']) || $data['user_id'] <= 0)) {
            $errors[] = 'Valid user ID is required';
        }
        
        if (isset($data['category_id']) && (!is_numeric($data['category_id']) || $data['category_id'] <= 0)) {
            $errors[] = 'Valid category ID is required';
        }
        
        if (isset($data['prep_minutes']) && (!is_numeric($data['prep_minutes']) || $data['prep_minutes'] < 0)) {
            $errors[] = 'Prep minutes must be a positive number';
        }
        
        if (isset($data['cook_minutes']) && (!is_numeric($data['cook_minutes']) || $data['cook_minutes'] < 0)) {
            $errors[] = 'Cook minutes must be a positive number';
        }
        
        if (isset($data['servings']) && (!is_numeric($data['servings']) || $data['servings'] < 1)) {
            $errors[] = 'Servings must be at least 1';
        }
        
        if (isset($data['difficulty_level']) && !in_array($data['difficulty_level'], ['Easy', 'Medium', 'Hard'])) {
            $errors[] = 'Difficulty level must be Easy, Medium, or Hard';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function processDataForCreate($data) {
        // Set defaults
        if (!isset($data['prep_minutes'])) $data['prep_minutes'] = 0;
        if (!isset($data['cook_minutes'])) $data['cook_minutes'] = 0;
        if (!isset($data['servings'])) $data['servings'] = 1;
        if (!isset($data['difficulty_level'])) $data['difficulty_level'] = 'Easy';
        
        // Trim strings
        if (isset($data['title'])) $data['title'] = trim($data['title']);
        if (isset($data['description'])) $data['description'] = trim($data['description']);
        if (isset($data['instructions'])) $data['instructions'] = trim($data['instructions']);
        
        return $data;
    }
    
    protected function processDataForUpdate($data, $id) {
        // Trim strings
        if (isset($data['title'])) $data['title'] = trim($data['title']);
        if (isset($data['description'])) $data['description'] = trim($data['description']);
        if (isset($data['instructions'])) $data['instructions'] = trim($data['instructions']);
        
        return $data;
    }
    
    protected function performSearch($query) {
        return $this->dao->search($query);
    }
    
    protected function canDelete($id) {
        try {
            // Check if recipe has reviews
            $reviewCount = $this->dao->query_unique(
                "SELECT COUNT(*) as count FROM reviews WHERE recipe_id = :id",
                ['id' => $id]
            );
            
            if ($reviewCount['count'] > 0) {
                return [
                    'can_delete' => false,
                    'message' => 'Cannot delete recipe with existing reviews'
                ];
            }
            
            return [
                'can_delete' => true,
                'message' => 'Recipe can be deleted'
            ];
        } catch (Exception $e) {
            return [
                'can_delete' => false,
                'message' => 'Error checking delete permissions'
            ];
        }
    }
}