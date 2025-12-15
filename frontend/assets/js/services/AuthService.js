var AuthService = {

    login: function (data, callback) {
        $.ajax({
            url: "http://localhost/api/auth/login",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function (res) {
                localStorage.setItem("token", res.data.token);
                localStorage.setItem("user", JSON.stringify(res.data));
                callback(true);
            },
            error: function () {
                callback(false);
            }
        });
    },

    register: function (data, callback) {
        $.ajax({
            url: "http://localhost/api/auth/register",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(data),
            success: function () {
                callback(true);
            },
            error: function () {
                callback(false);
            }
        });
    },

    getUser: function () {
        return JSON.parse(localStorage.getItem("user"));
    },

    isAdmin: function () {
        let u = this.getUser();
        return u && u.role === "admin";
    },

    logout: function () {
        localStorage.clear();
        window.location.hash = "#login";
    }
};
