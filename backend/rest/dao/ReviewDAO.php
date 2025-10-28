<?php

require_once 'BaseDAO.php';

/**
 * ReviewDAO - Data Access Object for reviews table
 * Handles CRUD operations for recipe reviews
 */
class ReviewDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct('reviews');
    }
    
    /**
     * Create a new review
     * @param array $data
     * @return int|false - Returns review ID on success, false on failure
     */
    public function create($data) {
        return $this->add($data);
    }
    
    /**
     * Update an existing review
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateReview($id, $data) {
        return $this->update($data, $id);
    }
    
    /**
     * Get all reviews for a specific recipe
     * @param int $recipeId
     * @return array
     */
    public function getByRecipe($recipeId) {
        return $this->query("SELECT r.*, u.name as user_name
                  FROM {$this->table_name} r 
                  INNER JOIN users u ON r.user_id = u.id 
                  WHERE r.recipe_id = :recipe_id 
                  ORDER BY r.created_at DESC", ['recipe_id' => $recipeId]);
    }
    
    /**
     * Get all reviews by a specific user
     * @param int $userId
     * @return array
     */
    public function getByUser($userId) {
        return $this->query("SELECT r.*, rec.title as recipe_title
                  FROM {$this->table_name} r 
                  INNER JOIN recipes rec ON r.recipe_id = rec.id 
                  WHERE r.user_id = :user_id 
                  ORDER BY r.created_at DESC", ['user_id' => $userId]);
    }
    
    /**
     * Get average rating for a recipe
     * @param int $recipeId
     * @return float|null
     */
    public function getAverageRating($recipeId) {
        $result = $this->query_unique("SELECT AVG(rating) as avg_rating FROM {$this->table_name} WHERE recipe_id = :recipe_id", ['recipe_id' => $recipeId]);
        return $result['avg_rating'] ? round((float) $result['avg_rating'], 2) : null;
    }
    
    /**
     * Get review count for a recipe
     * @param int $recipeId
     * @return int
     */
    public function getReviewCount($recipeId) {
        $result = $this->query_unique("SELECT COUNT(*) as count FROM {$this->table_name} WHERE recipe_id = :recipe_id", ['recipe_id' => $recipeId]);
        return (int) $result['count'];
    }
}