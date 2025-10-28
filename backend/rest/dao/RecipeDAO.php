<?php

require_once 'BaseDAO.php';

/**
 * RecipeDAO - Data Access Object for recipes table
 * Handles CRUD operations for recipes
 */
class RecipeDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct('recipes');
    }
    
    /**
     * Create a new recipe
     * @param array $data
     * @return int|false - Returns recipe ID on success, false on failure
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing recipe
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateRecipe($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get recipe with additional information (user, category, average rating)
     * @param int $id
     * @return array|null
     */
    public function getByIdWithDetails($id) {
        return $this->query_unique("SELECT r.*, u.name as user_name, c.name as category_name,
                         AVG(rev.rating) as avg_rating, COUNT(rev.id) as review_count
                  FROM {$this->table_name} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  LEFT JOIN categories c ON r.category_id = c.id 
                  LEFT JOIN reviews rev ON r.id = rev.recipe_id
                  WHERE r.id = :id 
                  GROUP BY r.id", ['id' => $id]);
    }
    
    /**
     * Get all recipes with basic details
     * @return array
     */
    public function getAllWithDetails() {
        return $this->query("SELECT r.*, u.name as user_name, c.name as category_name,
                         AVG(rev.rating) as avg_rating, COUNT(rev.id) as review_count
                  FROM {$this->table_name} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  LEFT JOIN categories c ON r.category_id = c.id 
                  LEFT JOIN reviews rev ON r.id = rev.recipe_id
                  GROUP BY r.id 
                  ORDER BY r.created_at DESC", []);
    }
    
    /**
     * Get recipes by user
     * @param int $userId
     * @return array
     */
    public function getByUser($userId) {
        return $this->query("SELECT r.*, c.name as category_name,
                         AVG(rev.rating) as avg_rating, COUNT(rev.id) as review_count
                  FROM {$this->table_name} r 
                  LEFT JOIN categories c ON r.category_id = c.id 
                  LEFT JOIN reviews rev ON r.id = rev.recipe_id
                  WHERE r.user_id = :user_id 
                  GROUP BY r.id 
                  ORDER BY r.created_at DESC", ['user_id' => $userId]);
    }
    
    /**
     * Search recipes by title or description
     * @param string $searchTerm
     * @return array
     */
    public function search($searchTerm) {
        $searchParam = '%' . $searchTerm . '%';
        return $this->query("SELECT r.*, u.name as user_name, c.name as category_name,
                         AVG(rev.rating) as avg_rating, COUNT(rev.id) as review_count
                  FROM {$this->table_name} r 
                  LEFT JOIN users u ON r.user_id = u.id 
                  LEFT JOIN categories c ON r.category_id = c.id 
                  LEFT JOIN reviews rev ON r.id = rev.recipe_id
                  WHERE r.title LIKE :search OR r.description LIKE :search 
                  GROUP BY r.id 
                  ORDER BY r.title", ['search' => $searchParam]);
    }
}