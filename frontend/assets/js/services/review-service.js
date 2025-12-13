let ReviewService = {

  getAll(callback) {
    RestClient.get("reviews", res => callback(res.data || res));
  },

  getById(id, callback) {
    RestClient.get("reviews/" + id, res => callback(res.data || res));
  },

  getByRecipe(recipe_id, callback) {
    RestClient.get("reviews/recipe/" + recipe_id, res => callback(res.data || res));
  },

  getByUser(user_id, callback) {
    RestClient.get("reviews/user/" + user_id, res => callback(res.data || res));
  },

  getAverage(recipe_id, callback) {
    RestClient.get("reviews/average/" + recipe_id, res => callback(res.data || res));
  },

  add(review, callback) {
    RestClient.post("reviews", review, callback);
  },

  update(id, review, callback) {
    RestClient.put("reviews/" + id, review, callback);
  },

  delete(id, callback) {
    RestClient.delete("reviews/" + id, callback);
  }
};
