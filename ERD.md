# ERD - Recipe Manager (Draft)

The application will use these entities and relationships. Below is a more precise ERD draft including field types, primary/foreign keys, and common constraints (intended for MySQL/Postgres relational schema):

- Users (id PK, name VARCHAR, email VARCHAR UNIQUE, password_hash VARCHAR, role_id FK -> Roles.id, created_at TIMESTAMP)
- Roles (id PK, name VARCHAR UNIQUE)
- Recipes (id PK, user_id FK -> Users.id, title VARCHAR, description TEXT, prep_minutes INT, cook_minutes INT, category_id FK -> Categories.id, created_at TIMESTAMP)
- Ingredients (id PK, name VARCHAR UNIQUE)
- RecipeIngredients (id PK, recipe_id FK -> Recipes.id, ingredient_id FK -> Ingredients.id, quantity DECIMAL, unit VARCHAR)
- Categories (id PK, name VARCHAR UNIQUE)
- Reviews (id PK, recipe_id FK -> Recipes.id, user_id FK -> Users.id, rating TINYINT, comment TEXT, created_at TIMESTAMP)

Mermaid ERD (detailed):

```mermaid
erDiagram
        USERS {
            int id PK
            varchar name
            varchar email UNIQUE
            varchar password_hash
            int role_id FK
            timestamp created_at
        }

        ROLES {
            int id PK
            varchar name UNIQUE
        }

        CATEGORIES {
            int id PK
            varchar name UNIQUE
        }

        RECIPES {
            int id PK
            int user_id FK
            varchar title
            text description
            int prep_minutes
            int cook_minutes
            int category_id FK
            timestamp created_at
        }

        INGREDIENTS {
            int id PK
            varchar name UNIQUE
        }

        RECIPE_INGREDIENTS {
            int id PK
            int recipe_id FK
            int ingredient_id FK
            decimal quantity
            varchar unit
        }

        REVIEWS {
            int id PK
            int recipe_id FK
            int user_id FK
            tinyint rating
            text comment
            timestamp created_at
        }

        ROLES ||--o{ USERS : assigns
        USERS ||--o{ RECIPES : creates
        USERS ||--o{ REVIEWS : writes
        CATEGORIES ||--o{ RECIPES : contains
        RECIPES ||--o{ RECIPE_INGREDIENTS : has
        INGREDIENTS ||--o{ RECIPE_INGREDIENTS : used_in
        RECIPES ||--o{ REVIEWS : reviewed_by
```

Notes:
- Cardinalities shown are the common defaults: one user can create many recipes; a recipe has many ingredients (via join table); a user can leave many reviews but one review belongs to a single recipe.
- Consider adding ON DELETE CASCADE for foreign keys like recipe_id -> RECIPE_INGREDIENTS and recipe_id -> REVIEWS if you want dependent rows removed automatically when a recipe is deleted.
- If you need tags, favorites, or media attachments, I can extend the ERD to include those tables and many-to-many relations.