// Sample dishes data
const sampleDishes = [
  { id:1, title:'Chocolate Cake', category:'Dessert', prep:45, image:'assets/img/dishes_1.jpg', rating:4.6, reviews:128, description:'Rich and moist chocolate layer cake with creamy frosting.' },
  { id:2, title:'Avocado Toast', category:'Breakfast', prep:10, image:'assets/img/dishes_2.jpg', rating:4.2, reviews:42, description:'Fresh avocado on toasted bread with spices.' },
  { id:3, title:'Grilled Salmon', category:'Dinner', prep:25, image:'assets/img/dishes_3.jpg', rating:4.8, reviews:63, description:'Perfectly grilled salmon fillet.' },
  { id:4, title:'Caesar Salad', category:'Lunch', prep:15, image:'assets/img/dishes_4.jpg', rating:4.1, reviews:34, description:'Crisp romaine lettuce with Caesar dressing.' },
  { id:5, title:'Pancakes', category:'Breakfast', prep:20, image:'assets/img/dishes_5.jpg', rating:4.4, reviews:57, description:'Fluffy homemade pancakes.' },
  { id:6, title:'Spaghetti Bolognese', category:'Dinner', prep:40, image:'assets/img/dishes_6.jpg', rating:4.5, reviews:91, description:'Classic Italian pasta with meat sauce.' },
  { id:7, title:'Chicken Tikka Masala', category:'Dinner', prep:50, image:'assets/img/dishes_7.jpg', rating:4.7, reviews:150, description:'Indian spiced chicken in creamy sauce.' },
  { id:8, title:'French Toast', category:'Breakfast', prep:18, image:'assets/img/dishes_8.jpg', rating:4.3, reviews:28, description:'Golden toast dipped in egg mixture.' },
  { id:9, title:'Beef Tacos', category:'Lunch', prep:30, image:'assets/img/dishes_9.jpg', rating:4.2, reviews:74, description:'Crispy tacos filled with seasoned beef.' },
  { id:10, title:'Margherita Pizza', category:'Dinner', prep:35, image:'assets/img/dishes_10.jpg', rating:4.6, reviews:212, description:'Classic pizza with tomato, mozzarella, and basil.' },
  { id:11, title:'Berry Smoothie', category:'Beverage', prep:5, image:'assets/img/dishes_11.jpg', rating:4.0, reviews:19, description:'Fresh mixed berry smoothie.' },
  { id:12, title:'Lemon Tart', category:'Dessert', prep:60, image:'assets/img/dishes_12.jpg', rating:4.9, reviews:44, description:'Tangy lemon tart with sweet crust.' }
];

function getDishes() {
  return new Promise(resolve => setTimeout(() => resolve(sampleDishes), 100));
}

function renderDishes() {
  getDishes().then(dishes => {

    const $list = $("#dishesList");
    const tpl = document.getElementById("dishCardTpl");

    $list.empty();

    dishes.forEach(dish => {
      const clone = tpl.content.cloneNode(true);

      $(clone).find(".dish-img").attr("src", dish.image);
      $(clone).find(".dish-title").text(dish.title);
      $(clone).find(".dish-cat").text(dish.category);
      $(clone).find(".rating").html("⭐".repeat(Math.round(dish.rating)));

      // DETAILS BUTTON EVENT
      $(clone).find("button").on("click", function () {
        showDishDetails(dish);
      });

      $list.append(clone);
    });

  });
}

function showDishDetails(dish) {
  $("#dishTitle").text(dish.title);
  $("#dishImage").attr("src", dish.image);
  $("#dishCategory").text(dish.category);
  $("#dishPrep").text(dish.prep);
  $("#dishDescription").text(dish.description || "No description available.");
  $("#dishRating").text(dish.rating + " ⭐");
  $("#dishReviews").text(dish.reviews + " reviews");

  $("#dishDetailsModal").modal("show");
}

$(document).ready(() => {
  renderDishes();
});
// --- SHOW CATEGORY DETAILS + LIST RECIPES ---
function showCategoryDetails(category) {

  // Set modal header
  $("#catModalTitle").text(category.name + " Category");
  $("#catModalImage").attr("src", category.image);
  $("#catModalName").text(category.name);
  $("#catModalCount").text(category.count + " recipes");

  // Filter recipes by selected category (FIXED)
  let recipes = sampleDishes.filter(r => r.category === category.name);

  let html = "";
  recipes.forEach(r => {
    html += `
      <div class="col-6 mb-3">
        <div class="p-2 border rounded shadow-sm">
          <img src="${r.image}" class="rounded mb-2" style="width:100%;height:120px;object-fit:cover;">
          <h6>${r.title}</h6>
          <p class="small text-muted">${r.prep} min | ⭐${r.rating}</p>
        </div>
      </div>
    `;
  });

  $("#catRecipesList").html(html);

  $("#categoryDetailsModal").modal("show");
}

// --- CLICK EVENT FOR CATEGORY CARDS ---
$(document).on("click", ".category-card", function () {
  const id = $(this).data("id");
  const cat = sampleCategories.find(c => c.id == id);
  showCategoryDetails(cat);
});
// OPEN ADD DISH MODAL
$(document).on("click", "#btnNewDish", function () {
    $("#addDishForm")[0].reset(); 
    $("#addDishModal").modal("show");
});

// SAVE NEW DISH
$(document).on("click", "#saveDishBtn", function () {

    let name = $("#dishName").val();
    let category = $("#dishCategoryInput").val();
    let prep = parseInt($("#dishPrep").val());
    let image = $("#dishImageUrl").val();
    let desc = $("#dishDescriptionInput").val();

    if (!name || !category || !prep || !image || !desc) {
        alert("All fields are required!");
        return;
    }

    // GENERATE UNIQUE ID
    let newId = sampleDishes.length
        ? Math.max(...sampleDishes.map(d => d.id)) + 1
        : 1;

    // ADD NEW DISH TO ARRAY
    sampleDishes.push({
        id: newId,
        title: name,
        category: category,
        prep: prep,
        image: image,
        rating: 0,
        reviews: 0,
        description: desc
    });

    $("#addDishModal").modal("hide");

    renderDishes(); // REFRESH LIST
});
