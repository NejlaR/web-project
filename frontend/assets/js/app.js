$.ajaxSetup({
    beforeSend: function (xhr) {
        let token = localStorage.getItem("token");
        if (token) {
            xhr.setRequestHeader("Authorization", "Bearer " + token);
        }
    }
});

console.log("APP FILE LOADED!");

// Fix navigation buttons (SPApp does not catch normal buttons)
$(document).on("click", "#btnLogin", () => window.location.hash = "login");
$(document).on("click", "#btnRegister", () => window.location.hash = "register");

$(document).ready(function () {

    // ================================
    //   INIT SPAPP
    // ================================
    var app = $.spapp({
        defaultView: "dashboard",
        templateDir: "views/"
    });

    // ================================
    //   GLOBAL HANDLER ZA data-link
    // ================================
    $(document).on("click", "[data-link]", function () {
        window.location.hash = $(this).data("link");
    });

    // ================================
    //   ROUTES
    // ================================

    // DASHBOARD
    app.route({
        view: "dashboard",
        load: "dashboard.html",
        onReady: function () {
            renderDashboard();
        }
    });

    // CATEGORIES
    app.route({
    view: "categories",
    load: "categories.html",
    onReady: function () {
        renderCategories();
    }
});



    // DISHES
    app.route({
        view: "dishes",
        load: "dishes.html",
        onReady: function () {
            renderDishes();
        }
    });

    // INGREDIENTS
    app.route({
        view: "ingredients",
        load: "ingredients.html",
        onReady: function () {
            renderIngredients();
        }
    });

    // ================================
    // LOGIN PAGE
    // ================================
    app.route({
        view: "login",
        load: "login.html",
        onReady: function () {

            console.log("Login view loaded!");

            $(document).off("submit", "#loginForm");

            $(document).on("submit", "#loginForm", function (e) {
                e.preventDefault();

                let credentials = {
                    email: $("#loginEmail").val(),
                    password: $("#loginPassword").val()
                };

                AuthService.login(credentials, function (success) {
                    if (success) {
                        alert("Login successful!");
                        window.location.hash = "#dashboard";
                    } else {
                        alert("Login failed");
                    }
                });
            });
        }
    });

    // PROFILE
    app.route({
        view: "profile",
        load: "profile.html",
        onReady: function () {
            renderProfile();
        }
    });

    // RECIPE FORM
    app.route({
        view: "recipe-form",
        load: "recipe-form.html",
        onReady: function () {
            initRecipeForm();
        }
    });

    // RECIPES LIST
    app.route({
        view: "recipes",
        load: "recipes.html",
        onReady: function () {
            renderRecipes();
        }
    });

    // ================================
    // REGISTER PAGE
    // ================================
    app.route({
        view: "register",
        load: "register.html",
        onReady: function () {

            console.log("Register view loaded!");

            $(document).off("submit", "#registerForm");

            $(document).on("submit", "#registerForm", function (e) {
                e.preventDefault();

                let data = {
                    name: $("#regName").val(),
                    email: $("#regEmail").val(),
                    password: $("#regPassword").val()
                };

                AuthService.register(data, function (success) {

                    if (!success) {
                        alert("Registration failed");
                        return;
                    }

                    alert("Account created!");

                    AuthService.login(
                        { email: data.email, password: data.password },
                        function () {
                            window.location.hash = "#dashboard";
                        }
                    );
                });
            });
        }
    });

    // FIX AUTOSCROLL
    $(document).on("click", "a.nav-link", function () {
        setTimeout(() => window.scrollTo(0, 0), 1);
    });

    // RUN THE APP
    app.run();
});


// ==========================================
//   CATEGORY ACTIONS (OSTAVLJENO KAKO JE)
// ==========================================

$(document).on("click", "#btnNewCategory", function () {
    console.log("Add category clicked!");
    $("#addCategoryForm")[0].reset();
    $("#addCategoryModal").modal("show");
});

$(document).on("click", "#saveCategoryBtn", function () {

    let name = $("#newCatName").val();
    let count = $("#newCatCount").val();
    let image = $("#newCatImage").val();

    if (!name || !count || !image) {
        alert("All fields are required!");
        return;
    }

    let newId = sampleCategories.length
        ? Math.max(...sampleCategories.map(c => c.id)) + 1
        : 1;

    sampleCategories.push({
        id: newId,
        name: name,
        count: parseInt(count),
        image: image,
        color: "#" + Math.floor(Math.random() * 16777215).toString(16)
    });

    $("#addCategoryModal").modal("hide");
    renderCategories();
});
