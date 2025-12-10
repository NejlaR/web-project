<?php

require_once 'BaseDAO.php';

class RecipeIngredientDAO extends BaseDAO {

    public function __construct() {
        // Tvoja tabela ima kolonu "id" kao primarni ključ → ovo je ispravno!
        parent::__construct('recipe_ingredients', 'id');
    }

    public function create($data) {
        return $this->add($data);
    }

    public function updateRecipeIngredient($id, $data) {
        return $this->update($data, $id);
    }

    /**
     * Get all ingredients for a specific recipe
     */
    public function getByRecipe($recipeId) {
        return $this->query("
            SELECT ri.*, i.name AS ingredient_name
            FROM recipe_ingredients ri
            INNER JOIN ingredients i 
                ON ri.ingredient_id = i.ingredient_id
            WHERE ri.recipe_id = :recipe_id
            ORDER BY ri.id
        ", ['recipe_id' => $recipeId]);
    }

    /**
     * Get all recipes that use a specific ingredient
     */
    public function getByIngredient($ingredientId) {
        return $this->query("
            SELECT ri.*, r.title AS recipe_title
            FROM recipe_ingredients ri
            INNER JOIN recipes r 
                ON ri.recipe_id = r.recipe_id
            WHERE ri.ingredient_id = :ingredient_id
            ORDER BY r.title
        ", ['ingredient_id' => $ingredientId]);
    }

    /**
     * Delete all ingredient links for a recipe
     */
    public function deleteByRecipe($recipeId) {
        $stmt = $this->connection->prepare("
            DELETE FROM recipe_ingredients 
            WHERE recipe_id = :recipe_id
        ");
        $stmt->bindValue(':recipe_id', $recipeId);
        return $stmt->execute();
    }
}
