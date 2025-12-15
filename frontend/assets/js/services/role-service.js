let RoleService = {

    getAll: function(callback, error_callback) {
        RestClient.get("role", function(res) {
            callback(res.data);
        }, error_callback);
    },

    getById: function(id, callback, error_callback) {
        RestClient.get("role/" + id, function(res) {
            callback(res.data);
        }, error_callback);
    },

    add: function(role, callback, error_callback) {
        RestClient.post("role", role, callback, error_callback);
    },

    update: function(id, role, callback, error_callback) {
        RestClient.put("role/" + id, role, callback, error_callback);
    },

    delete: function(id, callback, error_callback) {
        RestClient.delete("role/" + id, callback, error_callback);
    },

    getByName: function(name, callback, error_callback) {
        RestClient.get("role/name/" + name, function(res){
            callback(res.data);
        }, error_callback);
    },

    exists: function(name, callback, error_callback) {
        RestClient.get("role/exists/" + name, function(res){
            callback(res.data);
        }, error_callback);
    },

    getAllOrdered: function(callback, error_callback) {
        RestClient.get("role/ordered", function(res){
            callback(res.data);
        }, error_callback);
    }
};
