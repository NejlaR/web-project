$(document).ready(function(){
var app = $.spapp({
    defaultView: "dashboard",
    templateDir: "views/"

});
app.route({
  view: "dashboard",
  load: "dashboard.html",
  onReady: function() {
    console.log("Dashboard učitan!");
    renderDashboard();
  }
});

app.route({
  view: "categories",
  load: "categories.html",
  onReady: function() {
    console.log("Categories učitane!");
    renderCategories();
  }
});

app.route({
    view:"dishes",
    load: "dishes.html",
    onReady: function() {
    renderDishes();
  }
});
app.route({
  view: "ingredients",
  load: "ingredients.html",
  onReady: function() {
    console.log("Ingredients page loaded!");
    renderIngredients();
  }
});


app.route({
    view:"login",
    load: "login.html"
    
});
app.route({
  view: "profile",
  load: "profile.html",
  onReady: function () {
    renderProfile();
  }
});

app.route({
    view:"recipe-form",
    load: "recipe-form.html"
});
app.route({
  view: "recipes",
  load: "recipes.html",
  onReady: function() {
    renderRecipes();
  }
});

app.route({
    view:"register",
    load: "register.html"
});
$(document).on("click", "a.nav-link", function(){
    setTimeout(()=> {
        window.scrollTo(0,0);
},1);
})

app.run();

});