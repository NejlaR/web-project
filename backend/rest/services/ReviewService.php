<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/ReviewDAO.php';
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/RecipeDAO.php';

/**
 * ReviewService - Business logic for review management
 * Handles review operations including creation, moderation, and statistics
 */
class ReviewService extends BaseService {
    
    private $userDAO;
    private $recipeDAO;
    
    public function __construct() {
        parent::__construct(new ReviewDAO());
        $this->userDAO = new UserDAO();
        $this->recipeDAO = new RecipeDAO();
    }
    
    /**
     * Get reviews for a specific recipe
     * @param int $recipeId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getReviewsForRecipe($recipeId, $limit = 10, $offset = 0) {
        try {
            $reviews = $this->dao->getByRecipeId($recipeId, $limit, $offset);
            
            return [
                'success' => true,
                'data' => $reviews,
                'message' => 'Reviews retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving reviews: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get reviews by a specific user
     * @param int $userId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getReviewsByUser($userId, $limit = 10, $offset = 0) {
        try {
            $reviews = $this->dao->getByUserId($userId, $limit, $offset);
            
            return [
                'success' => true,
                'data' => $reviews,
                'message' => 'User reviews retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving user reviews: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get average rating for a recipe
     * @param int $recipeId
     * @return array
     */
    public function getRecipeRating($recipeId) {
        try {
            $stats = $this->dao->getRecipeRatingStats($recipeId);
            
            return [
                'success' => true,
                'data' => $stats,
                'message' => 'Recipe rating retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving recipe rating: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if user has already reviewed a recipe
     * @param int $userId
     * @param int $recipeId
     * @return array
     */
    public function hasUserReviewedRecipe($userId, $recipeId) {
        try {
            $existingReview = $this->dao->getUserReviewForRecipe($userId, $recipeId);
            
            return [
                'success' => true,
                'data' => ['has_reviewed' => !is_null($existingReview)],
                'message' => $existingReview ? 'User has reviewed this recipe' : 'User has not reviewed this recipe'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error checking review status: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get recent reviews
     * @param int $limit
     * @return array
     */
    public function getRecentReviews($limit = 10) {
        try {
            $reviews = $this->dao->query(
                "SELECT r.*, u.username, rec.title as recipe_title 
                 FROM reviews r 
                 INNER JOIN users u ON r.user_id = u.id 
                 INNER JOIN recipes rec ON r.recipe_id = rec.id 
                 ORDER BY r.created_at DESC 
                 LIMIT :limit",
                ['limit' => $limit]
            );
            
            return [
                'success' => true,
                'data' => $reviews,
                'message' => 'Recent reviews retrieved successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving recent reviews: ' . $e->getMessage()
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
                "SELECT r.*, AVG(rev.rating) as avg_rating, COUNT(rev.id) as review_count 
                 FROM recipes r 
                 INNER JOIN reviews rev ON r.id = rev.recipe_id 
                 GROUP BY r.id 
                 HAVING COUNT(rev.id) >= 3 
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
    
    // Required abstract method implementations
    protected function validateCreate($data) {
        $errors = [];
        
        // Validate required fields
        if (empty($data['user_id'])) {
            $errors[] = 'User ID is required';
        } else {
            // Check if user exists
            $user = $this->userDAO->read($data['user_id']);
            if (!$user) {
                $errors[] = 'Invalid user ID';
            }
        }
        
        if (empty($data['recipe_id'])) {
            $errors[] = 'Recipe ID is required';
        } else {
            // Check if recipe exists
            $recipe = $this->recipeDAO->read($data['recipe_id']);
            if (!$recipe) {
                $errors[] = 'Invalid recipe ID';
            }
        }
        
        if (!isset($data['rating']) || $data['rating'] === '') {
            $errors[] = 'Rating is required';
        } else {
            $rating = intval($data['rating']);
            if ($rating < 1 || $rating > 5) {
                $errors[] = 'Rating must be between 1 and 5';
            }
        }
        
        // Check for existing review by same user for same recipe
        if (!empty($data['user_id']) && !empty($data['recipe_id'])) {
            $existingReview = $this->dao->getUserReviewForRecipe($data['user_id'], $data['recipe_id']);
            if ($existingReview) {
                $errors[] = 'User has already reviewed this recipe';
            }
        }
        
        // Validate optional fields
        if (isset($data['comment']) && strlen($data['comment']) > 1000) {
            $errors[] = 'Comment must be 1000 characters or less';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function validateUpdate($data, $id) {
        $errors = [];
        
        // Get existing review to check ownership
        $existingReview = $this->dao->read($id);
        if (!$existingReview) {
            $errors[] = 'Review not found';
            return ['valid' => false, 'errors' => $errors];
        }
        
        // Validate rating if provided
        if (isset($data['rating'])) {
            $rating = intval($data['rating']);
            if ($rating < 1 || $rating > 5) {
                $errors[] = 'Rating must be between 1 and 5';
            }
        }
        
        // Validate comment if provided
        if (isset($data['comment']) && strlen($data['comment']) > 1000) {
            $errors[] = 'Comment must be 1000 characters or less';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    protected function processDataForCreate($data) {
        // Trim comment
        if (isset($data['comment'])) {
            $data['comment'] = trim($data['comment']);
        }
        
        // Set timestamps
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $data;
    }
    
    protected function processDataForUpdate($data, $id) {
        // Trim comment
        if (isset($data['comment'])) {
            $data['comment'] = trim($data['comment']);
        }
        
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $data;
    }
    
    protected function performSearch($query) {
        return $this->dao->search($query);
    }
    
    protected function canDelete($id) {
        // Users can delete their own reviews
        // Admins can delete any review
        return [
            'can_delete' => true,
            'message' => 'Review can be deleted'
        ];
    }
}