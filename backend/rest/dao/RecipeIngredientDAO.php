<?php

require_once 'BaseDAO.php';

/**
 * RecipeIngredientDAO - Data Access Object for recipe_ingredients table
 * Handles CRUD operations for recipe-ingredient relationships
 */
class RecipeIngredientDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct('recipe_ingredients');
    }
    
    /**
     * Create a new recipe-ingredient relationship
     * @param array $data
     * @return int|false - Returns ID on success, false on failure
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing recipe-ingredient relationship
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateRecipeIngredient($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get all ingredients for a specific recipe
     * @param int $recipeId
     * @return array
     */
    public function getByRecipe($recipeId) {
        return $this->query("SELECT ri.*, i.name as ingredient_name, i.description as ingredient_description
                  FROM {$this->table_name} ri 
                  INNER JOIN ingredients i ON ri.ingredient_id = i.id 
                  WHERE ri.recipe_id = :recipe_id 
                  ORDER BY ri.id", ['recipe_id' => $recipeId]);
    }
    
    /**
     * Get all recipes that use a specific ingredient
     * @param int $ingredientId
     * @return array
     */
    public function getByIngredient($ingredientId) {
        return $this->query("SELECT ri.*, r.title as recipe_title, r.description as recipe_description
                  FROM {$this->table_name} ri 
                  INNER JOIN recipes r ON ri.recipe_id = r.id 
                  WHERE ri.ingredient_id = :ingredient_id 
                  ORDER BY r.title", ['ingredient_id' => $ingredientId]);
    }
    
    /**
     * Delete all ingredients for a recipe
     * @param int $recipeId
     * @return bool
     */
    public function deleteByRecipe($recipeId) {
        $stmt = $this->connection->prepare("DELETE FROM {$this->table_name} WHERE recipe_id = :recipe_id");
        $stmt->bindValue(':recipe_id', $recipeId);
        return $stmt->execute();
    }
}