let RecipeService = {

  getAll(callback) {
    RestClient.get("recipes", res => callback(res.data || res));
  },

  getById(id, callback) {
    RestClient.get("recipes/" + id, res => callback(res.data || res));
  },

  getDetails(id, callback) {
    RestClient.get("recipes/" + id + "/details", res => callback(res.data || res));
  },

  search(term, callback) {
    RestClient.get("recipes/search/" + term, res => callback(res.data || res));
  },

  getByUser(user_id, callback) {
    RestClient.get("recipes/user/" + user_id, res => callback(res.data || res));
  },

  add(recipe, callback) {
    RestClient.post("recipes", recipe, callback);
  },

  update(id, recipe, callback) {
    RestClient.put("recipes/" + id, recipe, callback);
  },

  delete(id, callback) {
    RestClient.delete("recipes/" + id, callback);
  }
};
