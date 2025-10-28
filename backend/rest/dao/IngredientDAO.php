<?php

require_once 'BaseDAO.php';

/**
 * IngredientDAO - Data Access Object for ingredients table
 * Handles CRUD operations for ingredients
 */
class IngredientDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct('ingredients');
    }
    
    /**
     * Create a new ingredient
     * @param array $data
     * @return int|false - Returns ingredient ID on success, false on failure
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing ingredient
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateIngredient($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get ingredient by name
     * @param string $name
     * @return array|null
     */
    public function getByName($name) {
        return $this->query_unique("SELECT * FROM {$this->table_name} WHERE name = :name", ['name' => $name]);
    }
    
    /**
     * Get ingredients with usage count in recipes
     * @return array
     */
    public function getAllWithUsageCount() {
        return $this->query("SELECT i.*, COUNT(ri.recipe_id) as usage_count 
                  FROM {$this->table_name} i 
                  LEFT JOIN recipe_ingredients ri ON i.id = ri.ingredient_id 
                  GROUP BY i.id 
                  ORDER BY i.name", []);
    }
    
    /**
     * Search ingredients by name or description
     * @param string $searchTerm
     * @return array
     */
    public function search($searchTerm) {
        $searchParam = '%' . $searchTerm . '%';
        return $this->query("SELECT * FROM {$this->table_name} 
                  WHERE name LIKE :search OR description LIKE :search 
                  ORDER BY name", ['search' => $searchParam]);
    }
}