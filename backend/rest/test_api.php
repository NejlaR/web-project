<?php
/**
 * Recipe Manager API Test Suite
 * Comprehensive testing of all API endpoints
 */

class APITester {
    private $baseUrl;
    private $results = [];
    
    public function __construct($baseUrl = 'http://localhost:8080') {
        $this->baseUrl = $baseUrl;
    }
    
    private function makeRequest($method, $endpoint, $data = null) {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['error' => $error, 'code' => 0];
        }
        
        return [
            'code' => $httpCode,
            'data' => json_decode($response, true),
            'raw' => $response
        ];
    }
    
    private function test($name, $method, $endpoint, $data = null, $expectedCode = 200) {
        echo "Testing: $name... ";
        $result = $this->makeRequest($method, $endpoint, $data);
        
        $success = ($result['code'] == $expectedCode);
        $this->results[] = [
            'name' => $name,
            'success' => $success,
            'expected_code' => $expectedCode,
            'actual_code' => $result['code'],
            'response' => $result
        ];
        
        echo $success ? "✅ PASS\n" : "❌ FAIL (Expected: $expectedCode, Got: {$result['code']})\n";
        
        if (!$success || isset($result['error'])) {
            echo "  Error: " . ($result['error'] ?? 'HTTP Error') . "\n";
            if (isset($result['data']['message'])) {
                echo "  Message: " . $result['data']['message'] . "\n";
            }
        }
        
        return $result;
    }
    
    public function runTests() {
        echo "🚀 Starting Recipe Manager API Test Suite\n";
        echo "========================================\n\n";
        
        // Test API Status
        $this->test('API Status', 'GET', '/');
        
        // Test Categories
        echo "\n📁 Testing Categories:\n";
        $this->test('Get All Categories', 'GET', '/categories');
        $this->test('Get Categories with Count', 'GET', '/categories/with-count');
        
        $categoryData = [
            'name' => 'Test Category',
            'description' => 'Test category description'
        ];
        $categoryResult = $this->test('Create Category', 'POST', '/categories', $categoryData, 201);
        
        if ($categoryResult['code'] == 201 && isset($categoryResult['data']['data']['id'])) {
            $categoryId = $categoryResult['data']['data']['id'];
            $this->test('Get Category by ID', 'GET', "/categories/$categoryId");
            
            $updateData = ['description' => 'Updated description'];
            $this->test('Update Category', 'PUT', "/categories/$categoryId", $updateData);
        }
        
        // Test Ingredients
        echo "\n🥕 Testing Ingredients:\n";
        $this->test('Get All Ingredients', 'GET', '/ingredients');
        $this->test('Get Ingredients with Usage', 'GET', '/ingredients/with-usage');
        $this->test('Get Most Used Ingredients', 'GET', '/ingredients/most-used?limit=5');
        
        $ingredientData = [
            'name' => 'Test Ingredient',
            'description' => 'Test ingredient description'
        ];
        $ingredientResult = $this->test('Create Ingredient', 'POST', '/ingredients', $ingredientData, 201);
        
        if ($ingredientResult['code'] == 201 && isset($ingredientResult['data']['data']['id'])) {
            $ingredientId = $ingredientResult['data']['data']['id'];
            $this->test('Get Ingredient by ID', 'GET', "/ingredients/$ingredientId");
        }
        
        // Test Users
        echo "\n👥 Testing Users:\n";
        $this->test('Get All Users', 'GET', '/users');
        
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role_id' => 3
        ];
        $userResult = $this->test('Create User', 'POST', '/users', $userData, 201);
        
        if ($userResult['code'] == 201 && isset($userResult['data']['data']['id'])) {
            $userId = $userResult['data']['data']['id'];
            $this->test('Get User by ID', 'GET', "/users/$userId");
        }
        
        // Test Authentication
        echo "\n🔐 Testing Authentication:\n";
        $registerData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123'
        ];
        $this->test('User Registration', 'POST', '/users/register', $registerData, 201);
        
        $loginData = [
            'email' => 'newuser@example.com',
            'password' => 'password123'
        ];
        $this->test('User Login', 'POST', '/users/login', $loginData);
        
        // Test Roles
        echo "\n👑 Testing Roles:\n";
        $this->test('Get All Roles', 'GET', '/roles');
        
        $roleData = [
            'name' => 'test_role'
        ];
        $roleResult = $this->test('Create Role', 'POST', '/roles', $roleData, 201);
        
        // Test Recipes
        echo "\n🍳 Testing Recipes:\n";
        $this->test('Get All Recipes', 'GET', '/recipes');
        $this->test('Search Recipes', 'GET', '/recipes/search?q=test');
        
        if (isset($userId) && isset($categoryId)) {
            $recipeData = [
                'user_id' => $userId,
                'title' => 'Test Recipe',
                'description' => 'Test recipe description',
                'category_id' => $categoryId,
                'prep_minutes' => 15,
                'cook_minutes' => 30,
                'servings' => 4,
                'difficulty_level' => 'Easy',
                'instructions' => 'Test instructions'
            ];
            $recipeResult = $this->test('Create Recipe', 'POST', '/recipes', $recipeData, 201);
            
            if ($recipeResult['code'] == 201 && isset($recipeResult['data']['data']['id'])) {
                $recipeId = $recipeResult['data']['data']['id'];
                $this->test('Get Recipe by ID', 'GET', "/recipes/$recipeId");
                $this->test('Get Recipes by Category', 'GET', "/recipes/category/$categoryId");
                $this->test('Get Recipes by User', 'GET', "/recipes/user/$userId");
            }
        }
        
        // Test Reviews
        echo "\n⭐ Testing Reviews:\n";
        $this->test('Get All Reviews', 'GET', '/reviews');
        
        if (isset($userId) && isset($recipeId)) {
            $reviewData = [
                'user_id' => $userId,
                'recipe_id' => $recipeId,
                'rating' => 5,
                'comment' => 'Great recipe!'
            ];
            $reviewResult = $this->test('Create Review', 'POST', '/reviews', $reviewData, 201);
            
            if ($reviewResult['code'] == 201) {
                $this->test('Get Reviews for Recipe', 'GET', "/reviews/recipe/$recipeId");
                $this->test('Get Reviews by User', 'GET', "/reviews/user/$userId");
                $this->test('Get Recipe Rating', 'GET', "/reviews/recipe/$recipeId/rating");
            }
        }
        
        // Test Recipe Ingredients
        echo "\n🥄 Testing Recipe Ingredients:\n";
        if (isset($recipeId) && isset($ingredientId)) {
            $this->test('Get Recipe Ingredients', 'GET', "/recipe-ingredients/recipe/$recipeId");
            
            $recipeIngredientData = [
                'recipe_id' => $recipeId,
                'ingredient_id' => $ingredientId,
                'quantity' => 2,
                'unit' => 'cups',
                'notes' => 'Test notes'
            ];
            $this->test('Add Ingredient to Recipe', 'POST', '/recipe-ingredients', $recipeIngredientData, 201);
        }
        
        echo "\n📊 Test Summary:\n";
        echo "===============\n";
        
        $total = count($this->results);
        $passed = count(array_filter($this->results, fn($r) => $r['success']));
        $failed = $total - $passed;
        
        echo "Total Tests: $total\n";
        echo "Passed: $passed ✅\n";
        echo "Failed: $failed ❌\n";
        echo "Success Rate: " . round(($passed / $total) * 100, 2) . "%\n\n";
        
        if ($failed > 0) {
            echo "Failed Tests:\n";
            foreach ($this->results as $result) {
                if (!$result['success']) {
                    echo "- {$result['name']} (Expected: {$result['expected_code']}, Got: {$result['actual_code']})\n";
                }
            }
        }
        
        return $this->results;
    }
}

// Run the tests
$tester = new APITester();
$results = $tester->runTests();