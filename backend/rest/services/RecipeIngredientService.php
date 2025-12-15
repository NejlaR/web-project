<?php

require_once __DIR__ . '/../dao/RecipeIngredientDAO.php';
require_once 'BaseService.php';

class RecipeIngredientService extends BaseService {

    public function __construct() {
        parent::__construct(new RecipeIngredientDAO());
    }

    // Get all ingredients for one recipe
    public function get_by_recipe($recipeId) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByRecipe($recipeId),
                "message" => "Ingredients for recipe retrieved"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Get all recipes that use a specific ingredient
    public function get_by_ingredient($ingredientId) {
        try {
            return [
                "success" => true,
                "data" => $this->dao->getByIngredient($ingredientId),
                "message" => "Recipes containing ingredient retrieved"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Delete all ingredient links for a recipe
    public function delete_by_recipe($recipeId) {
        try {
            return [
                "success" => $this->dao->deleteByRecipe($recipeId),
                "message" => "All ingredients removed from recipe"
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
