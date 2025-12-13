let RestClient = {

  request: function (url, method, data, callback, error_callback) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      contentType: "application/json",
      data: data ? JSON.stringify(data) : null,

      beforeSend: function (xhr) {
        let token = localStorage.getItem("token");
        if (token) {
          xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
      }
    })
    .done(function (response) {
      if (callback) callback(response);
    })
    .fail(function (jqXHR) {
      if (error_callback) error_callback(jqXHR);
      else alert(jqXHR.responseJSON?.error || "Server error");
    });
  },

  get: function (url, cb, err) {
    RestClient.request(url, "GET", null, cb, err);
  },

  post: function (url, data, cb, err) {
    RestClient.request(url, "POST", data, cb, err);
  },

  put: function (url, data, cb, err) {
    RestClient.request(url, "PUT", data, cb, err);
  },

  patch: function (url, data, cb, err) {
    RestClient.request(url, "PATCH", data, cb, err);
  },

  delete: function (url, cb, err) {
    RestClient.request(url, "DELETE", null, cb, err);
  }
};
