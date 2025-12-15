console.log("recipe-form.js loaded");

// =========================================
//  SAMPLE RECEPTI (17 KOM) – BEZ SLIKA
// =========================================
const sampleRecipes = [
    { id: 1, title: "Chocolate Cake", category: "Dessert", prep: 45, ingredients: "Flour, Eggs, Sugar, Cocoa" },
    { id: 2, title: "Avocado Toast", category: "Breakfast", prep: 10, ingredients: "Avocado, Bread, Salt, Pepper" },
    { id: 3, title: "Spaghetti Bolognese", category: "Dinner", prep: 35, ingredients: "Pasta, Beef, Tomato Sauce" },
    { id: 4, title: "Caesar Salad", category: "Lunch", prep: 15, ingredients: "Lettuce, Chicken, Croutons, Caesar dressing" },
    { id: 5, title: "Pancakes", category: "Breakfast", prep: 20, ingredients: "Flour, Eggs, Milk, Sugar" },
    { id: 6, title: "Chicken Soup", category: "Dinner", prep: 40, ingredients: "Chicken, Carrots, Onion, Salt" },
    { id: 7, title: "Lemon Tart", category: "Dessert", prep: 30, ingredients: "Lemon, Sugar, Eggs, Butter" },
    { id: 8, title: "French Toast", category: "Breakfast", prep: 12, ingredients: "Bread, Eggs, Milk, Cinnamon" },
    { id: 9, title: "Beef Tacos", category: "Lunch", prep: 25, ingredients: "Beef, Tortillas, Cheese, Lettuce" },
    { id: 10, title: "Greek Salad", category: "Lunch", prep: 10, ingredients: "Tomato, Cucumber, Feta, Olives" },
    { id: 11, title: "Fried Rice", category: "Dinner", prep: 20, ingredients: "Rice, Egg, Peas, Carrots, Soy sauce" },
    { id: 12, title: "Fruit Bowl", category: "Breakfast", prep: 5, ingredients: "Banana, Apple, Berries, Honey" },
    { id: 13, title: "Grilled Chicken Breast", category: "Dinner", prep: 30, ingredients: "Chicken, Olive oil, Salt, Pepper" },
    { id: 14, title: "Tomato Soup", category: "Lunch", prep: 25, ingredients: "Tomatoes, Garlic, Onion, Cream" },
    { id: 15, title: "Muffins", category: "Dessert", prep: 35, ingredients: "Flour, Eggs, Sugar, Blueberries" },
    { id: 16, title: "Scrambled Eggs", category: "Breakfast", prep: 7, ingredients: "Eggs, Butter, Salt" },
    { id: 17, title: "Veggie Wrap", category: "Lunch", prep: 15, ingredients: "Tortilla, Lettuce, Tomato, Corn" }
];

let editId = null;

// =========================================
//  INIT RECIPE FORM (called by app.js)
// =========================================
function initRecipeForm() {

    console.log("initRecipeForm called");

    let params = new URLSearchParams(window.location.hash.split("?")[1]);
    editId = params.get("id");

    // ------------------------
    // EDIT MODE
    // ------------------------
    if (editId) {
        let recipe = sampleRecipes.find(r => r.id == editId);

        if (recipe) {
            $("#formTitle").text("Edit Recipe");
            $("#title").val(recipe.title);
            $("#category").val(recipe.category);
            $("#prep").val(recipe.prep);
            $("#ingredients").val(recipe.ingredients);
        }
    } else {
        $("#formTitle").text("Add Recipe");
        $("#recipeForm")[0].reset();
    }

    // ------------------------
    // SAVE BUTTON HANDLER
    // ------------------------
    $("#saveRecipe").off("click").on("click", function (e) {
        e.preventDefault();

        let title = $("#title").val();
        let category = $("#category").val();
        let prep = $("#prep").val();
        let ingredients = $("#ingredients").val();

        if (!title || !category || !prep) {
            alert("All fields are required!");
            return;
        }

        // UPDATE EXISTING RECIPE
        if (editId) {
            let r = sampleRecipes.find(x => x.id == editId);
            r.title = title;
            r.category = category;
            r.prep = parseInt(prep);
            r.ingredients = ingredients;

            alert("Recipe updated!");
        }
        // ADD NEW RECIPE
        else {
            let newId = Math.max(...sampleRecipes.map(r => r.id)) + 1;

            sampleRecipes.push({
                id: newId,
                title,
                category,
                prep: parseInt(prep),
                ingredients
            });

            alert("Recipe added!");
        }

        window.location.href = "#recipes";
    });
}
