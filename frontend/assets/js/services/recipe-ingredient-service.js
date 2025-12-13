let RecipeIngredientService = {

    getAll(callback, error_callback) {
        RestClient.get("recipe-ingredients",
            res => callback(res.data || res),
            error_callback
        );
    },

    getById(id, callback, error_callback) {
        RestClient.get("recipe-ingredients/" + id,
            res => callback(res.data || res),
            error_callback
        );
    },

    add(data, callback, error_callback) {
        RestClient.post("recipe-ingredients", data, callback, error_callback);
    },

    update(id, data, callback, error_callback) {
        RestClient.put("recipe-ingredients/" + id, data, callback, error_callback);
    },

    delete(id, callback, error_callback) {
        RestClient.delete("recipe-ingredients/" + id, callback, error_callback);
    },

    getByRecipe(recipeId, callback, error_callback) {
        RestClient.get("recipe-ingredients/recipe/" + recipeId,
            res => callback(res.data || res),
            error_callback
        );
    },

    getByIngredient(ingredientId, callback, error_callback) {
        RestClient.get("recipe-ingredients/ingredient/" + ingredientId,
            res => callback(res.data || res),
            error_callback
        );
    },

    deleteAllForRecipe(recipeId, callback, error_callback) {
        RestClient.delete("recipe-ingredients/recipe/" + recipeId, callback, error_callback);
    }
};
