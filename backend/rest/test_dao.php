<?php

/**
 * DAO Test Script
 * Simple test file to demonstrate DAO functionality
 * This file can be used to test the DAO classes and database operations
 */

// Include configuration and autoloader
require_once 'Config.php';
require_once 'DAOAutoloader.php';

// Error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    echo "<h1>Recipe Manager DAO Test</h1>\n";
    
    // Test database connection through BaseDAO
    echo "<h2>Testing Database Connection</h2>\n";
    $roleDAO = new RoleDAO();
    echo "✓ Database connection successful<br>\n";
    
    // Test Role operations
    echo "<h2>Testing Role DAO</h2>\n";
    $roles = $roleDAO->getAll();
    echo "Found " . count($roles) . " roles:<br>\n";
    foreach ($roles as $role) {
        echo "- {$role['name']} (ID: {$role['id']})<br>\n";
    }
    
    // Test Category operations
    echo "<h2>Testing Category DAO</h2>\n";
    $categoryDAO = new CategoryDAO();
    $categories = $categoryDAO->getAllWithRecipeCount();
    echo "Found " . count($categories) . " categories:<br>\n";
    foreach ($categories as $category) {
        echo "- {$category['name']}: {$category['recipe_count']} recipes<br>\n";
    }
    
    // Test Ingredient operations
    echo "<h2>Testing Ingredient DAO</h2>\n";
    $ingredientDAO = new IngredientDAO();
    $ingredients = $ingredientDAO->getAllWithUsageCount();
    echo "Found " . count($ingredients) . " ingredients:<br>\n";
    foreach (array_slice($ingredients, 0, 5) as $ingredient) {
        echo "- {$ingredient['name']}: used in {$ingredient['usage_count']} recipes<br>\n";
    }
    
    // Test User operations (create a test user)
    echo "<h2>Testing User DAO</h2>\n";
    $userDAO = new UserDAO();
    
    // Check if test user exists
    $testEmail = 'test@example.com';
    $existingUser = $userDAO->getByEmail($testEmail);
    
    if (!$existingUser) {
        $userData = [
            'name' => 'Test User',
            'email' => $testEmail,
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role_id' => 3 // Assuming role_id 3 is 'user'
        ];
        
        $userId = $userDAO->create($userData);
        if ($userId) {
            echo "✓ Created test user with ID: $userId<br>\n";
        } else {
            echo "✗ Failed to create test user<br>\n";
        }
    } else {
        echo "✓ Test user already exists (ID: {$existingUser['id']})<br>\n";
    }
    
    $users = $userDAO->getAllWithRoles();
    echo "Total users: " . count($users) . "<br>\n";
    
    // Test Recipe operations
    echo "<h2>Testing Recipe DAO</h2>\n";
    $recipeDAO = new RecipeDAO();
    $recipes = $recipeDAO->getAllWithDetails();
    echo "Found " . count($recipes) . " recipes<br>\n";
    
    if (count($recipes) > 0) {
        $recipe = $recipes[0];
        echo "Sample recipe: {$recipe['title']} by {$recipe['user_name']}<br>\n";
        
        // Test recipe ingredients
        $recipeIngredientDAO = new RecipeIngredientDAO();
        $recipeIngredients = $recipeIngredientDAO->getByRecipe($recipe['id']);
        echo "Recipe has " . count($recipeIngredients) . " ingredients<br>\n";
    }
    
    // Test Review operations
    echo "<h2>Testing Review DAO</h2>\n";
    $reviewDAO = new ReviewDAO();
    $recentReviews = $reviewDAO->getRecent(5);
    echo "Found " . count($recentReviews) . " recent reviews<br>\n";
    
    echo "<h2>✓ All DAO tests completed successfully!</h2>\n";
    
} catch (Exception $e) {
    echo "<h2>✗ Error occurred:</h2>\n";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>\n";
    echo "<p>Stack trace:</p>\n";
    echo "<pre>" . $e->getTraceAsString() . "</pre>\n";
}

echo "<hr>\n";
echo "<p><strong>Note:</strong> Make sure to:</p>\n";
echo "<ul>\n";
echo "<li>Create the database using the database.sql file</li>\n";
echo "<li>Update database credentials in Config.php if needed</li>\n";
echo "<li>Ensure your web server has access to the database</li>\n";
echo "</ul>\n";
?>