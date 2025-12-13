let IngredientService = {

  getAll(callback) {
    RestClient.get("ingredients", res => callback(res.data || res));
  },

  getById(id, callback) {
    RestClient.get("ingredients/" + id, res => callback(res.data || res));
  },

  search(term, callback) {
    RestClient.get("ingredients/search/" + term, res => callback(res.data || res));
  },

  getWithCount(callback) {
    RestClient.get("ingredients/with-count", res => callback(res.data || res));
  },

  add(ingredient, callback) {
    RestClient.post("ingredients", ingredient, callback);
  },

  update(id, ingredient, callback) {
    RestClient.put("ingredients/" + id, ingredient, callback);
  },

  delete(id, callback) {
    RestClient.delete("ingredients/" + id, callback);
  }
};
