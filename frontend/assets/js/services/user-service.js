var UserService = {

    // login: prima { email, password }
    login: function (credentials, callback) {

        RestClient.post(
            "auth/login",
            credentials,
            function (res) {
                if (!res || !res.data || !res.data.token) {
                    callback(false, "Invalid server response");
                    return;
                }

                // sačuvaj user podatke i token
                localStorage.setItem("token", res.data.token);
                localStorage.setItem("user", JSON.stringify(res.data));

                callback(true, res.data);
            },
            function (xhr) {
                callback(false, xhr.responseJSON?.error || "Login failed");
            }
        );
    },

    // register: prima { name, email, password }
    register: function (data, callback) {

        RestClient.post(
            "auth/register",
            data,
            function (res) {
                if (!res || !res.data) {
                    callback(false, "Invalid server response");
                    return;
                }
                callback(true, res.data);
            },
            function (xhr) {
                callback(false, xhr.responseJSON?.error || "Registration failed");
            }
        );
    },

    logout: function () {
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        window.location.hash = "#login";
    },

    isLoggedIn: function () {
        return localStorage.getItem("token") !== null;
    },

    getUser: function () {
        let u = localStorage.getItem("user");
        return u ? JSON.parse(u) : null;
    }
};
