# Recipe Manager - Database and DAO Implementation

This directory contains the Database schema and Data Access Object (DAO) implementation for the Recipe Manager project.

## Database Schema

The database consists of 7 entities with proper relationships:

### Entities
1. **roles** - User roles (admin, chef, user)
2. **users** - Application users with authentication
3. **categories** - Recipe categories (Appetizers, Main Course, etc.)
4. **ingredients** - Individual ingredients with nutritional info
5. **recipes** - Recipe details with cooking information
6. **recipe_ingredients** - Junction table linking recipes to ingredients
7. **reviews** - User reviews and ratings for recipes

### Database Setup

1. **Create the database:**
   ```sql
   -- Import the database.sql file into MySQL
   mysql -u root -p < database.sql
   ```

2. **Update configuration:**
   - Edit `Config.php` to match your database credentials
   - Default settings are for XAMPP (localhost, root, no password)

## DAO Layer

The DAO (Data Access Object) layer provides a clean interface for database operations.

### Base DAO Features
- Database connection management
- Common CRUD operations (Create, Read, Update, Delete)
- Pagination support
- Search functionality
- Transaction support

### Available DAO Classes

#### 1. RoleDAO
```php
$roleDAO = new RoleDAO();
$roles = $roleDAO->getAll();
$role = $roleDAO->getByName('admin');
```

#### 2. UserDAO
```php
$userDAO = new UserDAO();
$users = $userDAO->getAllWithRoles();
$user = $userDAO->getByEmail('user@example.com');
$userId = $userDAO->create($userData);
```

#### 3. CategoryDAO
```php
$categoryDAO = new CategoryDAO();
$categories = $categoryDAO->getAllWithRecipeCount();
$categoryId = $categoryDAO->create(['name' => 'Desserts', 'description' => 'Sweet treats']);
```

#### 4. IngredientDAO
```php
$ingredientDAO = new IngredientDAO();
$ingredients = $ingredientDAO->getAllWithUsageCount();
$ingredientId = $ingredientDAO->create(['name' => 'Tomato', 'description' => 'Fresh tomatoes']);
```

#### 5. RecipeDAO
```php
$recipeDAO = new RecipeDAO();
$recipes = $recipeDAO->getAllWithDetails();
$recipe = $recipeDAO->getByIdWithDetails($id);
$recipeId = $recipeDAO->create($recipeData);
```

#### 6. RecipeIngredientDAO
```php
$recipeIngredientDAO = new RecipeIngredientDAO();
$ingredients = $recipeIngredientDAO->getByRecipe($recipeId);
$recipeIngredientDAO->updateRecipeIngredients($recipeId, $ingredientsArray);
```

#### 7. ReviewDAO
```php
$reviewDAO = new ReviewDAO();
$reviews = $reviewDAO->getByRecipe($recipeId);
$avgRating = $reviewDAO->getAverageRating($recipeId);
$reviewId = $reviewDAO->create($reviewData);
```

## Usage Examples

### Basic CRUD Operations

```php
// Include the autoloader
require_once 'DAOAutoloader.php';

// Create a new category
$categoryDAO = new CategoryDAO();
$categoryId = $categoryDAO->create([
    'name' => 'Breakfast',
    'description' => 'Morning meals'
]);

// Read categories
$categories = $categoryDAO->getAll();

// Update a category
$categoryDAO->update($categoryId, [
    'description' => 'Healthy morning meals'
]);

// Delete a category
$categoryDAO->delete($categoryId);
```

### Advanced Operations

```php
// Get recipes with ingredients and reviews
$recipeDAO = new RecipeDAO();
$recipe = $recipeDAO->getByIdWithDetails($recipeId);

// Get recipe ingredients
$recipeIngredientDAO = new RecipeIngredientDAO();
$ingredients = $recipeIngredientDAO->getByRecipe($recipeId);

// Get recipe reviews and rating
$reviewDAO = new ReviewDAO();
$reviews = $reviewDAO->getByRecipe($recipeId);
$avgRating = $reviewDAO->getAverageRating($recipeId);
```

### Search Operations

```php
// Search recipes by title
$recipes = $recipeDAO->search('chicken');

// Search ingredients by name
$ingredients = $ingredientDAO->search('tomato');

// Get recipes by category
$recipes = $recipeDAO->getByCategory($categoryId);

// Get top rated recipes
$topRecipes = $recipeDAO->getTopRated(10);
```

## Testing

### Test the DAO Implementation

1. **Run the test script:**
   ```
   http://localhost/web-project/backend/rest/test_dao.php
   ```

2. **Test API endpoints:**
   ```
   http://localhost/web-project/backend/rest/
   http://localhost/web-project/backend/rest/categories
   http://localhost/web-project/backend/rest/ingredients
   http://localhost/web-project/backend/rest/recipes
   ```

## File Structure

```
backend/rest/
├── dao/
│   ├── BaseDAO.php           # Base DAO class with common operations
│   ├── RoleDAO.php           # Role management
│   ├── UserDAO.php           # User management
│   ├── CategoryDAO.php       # Category management
│   ├── IngredientDAO.php     # Ingredient management
│   ├── RecipeDAO.php         # Recipe management
│   ├── RecipeIngredientDAO.php # Recipe-ingredient relationships
│   └── ReviewDAO.php         # Review and rating management
├── Config.php                # Database configuration
├── DAOAutoloader.php         # Automatic class loading
├── index.php                 # API entry point with sample endpoints
└── test_dao.php             # DAO testing script
```

## Features Implemented

### Database Features
✅ 7 related entities with proper foreign keys  
✅ Indexes for performance optimization  
✅ Sample data for testing  
✅ Cascading deletes where appropriate  

### DAO Features
✅ Full CRUD operations for all entities  
✅ Advanced search and filtering  
✅ Pagination support  
✅ Transaction support  
✅ Relationship handling  
✅ Data validation  
✅ Error handling  

### API Features
✅ RESTful endpoint structure  
✅ JSON responses  
✅ CORS support  
✅ Error handling  
✅ Status endpoints  

## Next Steps (Future Milestones)

1. **Authentication & Authorization**
   - JWT token implementation
   - User session management
   - Role-based access control

2. **Complete REST API**
   - Full CRUD endpoints for all entities
   - Request validation
   - File upload for recipe images

3. **Advanced Features**
   - Recipe search by ingredients
   - User favorites
   - Recipe recommendations
   - Nutrition calculations

## Notes

- Database credentials are configured for XAMPP by default
- All passwords should be hashed using `password_hash()` function
- The system supports JSON fields for nutritional information
- Foreign key constraints ensure data integrity
- The DAO layer is designed to be easily extensible

This implementation satisfies the milestone requirements for database creation and DAO layer with full CRUD functionality for at least 5 entities.