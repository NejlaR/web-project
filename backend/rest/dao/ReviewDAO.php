<?php

require_once 'BaseDAO.php';

class ReviewDAO extends BaseDAO {
    
    public function __construct() {
        parent::__construct('reviews', 'review_id');
    }

    public function create($data) {
        return $this->add($data);
    }

    public function updateReview($id, $data) {
        return $this->update($data, $id);
    }

    /* ==========================
       GET REVIEWS BY RECIPE
       ========================== */
    public function getByRecipe($recipeId) {
        return $this->query("
            SELECT r.*, u.name AS user_name
            FROM reviews r
            INNER JOIN users u ON r.user_id = u.user_id
            WHERE r.recipe_id = :recipe_id
            ORDER BY r.created_at DESC
        ", ['recipe_id' => $recipeId]);
    }

    /* ==========================
       GET REVIEWS BY USER
       ========================== */
    public function getByUser($userId) {
        return $this->query("
            SELECT r.*, rec.title AS recipe_title
            FROM reviews r
            INNER JOIN recipes rec ON r.recipe_id = rec.recipe_id
            WHERE r.user_id = :user_id
            ORDER BY r.created_at DESC
        ", ['user_id' => $userId]);
    }

    public function getAverageRating($recipeId) {
        $result = $this->query_unique("
            SELECT AVG(rating) AS avg_rating 
            FROM reviews 
            WHERE recipe_id = :recipe_id
        ", ['recipe_id' => $recipeId]);

        return $result['avg_rating'] ? round((float)$result['avg_rating'], 2) : null;
    }

    public function getReviewCount($recipeId) {
        $result = $this->query_unique("
            SELECT COUNT(*) AS count 
            FROM reviews 
            WHERE recipe_id = :recipe_id
        ", ['recipe_id' => $recipeId]);

        return (int)$result['count'];
    }
}
