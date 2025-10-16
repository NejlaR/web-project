// Sample dishes data - would come from API in real app
const sampleDishes = [
  { id:1, title:'Chocolate Cake', category:'Dessert', prep:45, image:'assets/img/dishes_1.jpg', rating:4.6, reviews:128, description:'Rich and moist chocolate layer cake with creamy frosting.' },
  { id:2, title:'Avocado Toast', category:'Breakfast', prep:10, image:'assets/img/dishes_2.jpg', rating:4.2, reviews:42 },
  { id:3, title:'Grilled Salmon', category:'Dinner', prep:25, image:'assets/img/dishes_3.jpg', rating:4.8, reviews:63 },
  { id:4, title:'Caesar Salad', category:'Lunch', prep:15, image:'assets/img/dishes_4.jpg', rating:4.1, reviews:34 },
  { id:5, title:'Pancakes', category:'Breakfast', prep:20, image:'assets/img/dishes_5.jpg', rating:4.4, reviews:57 },
  { id:6, title:'Spaghetti Bolognese', category:'Dinner', prep:40, image:'assets/img/dishes_6.jpg', rating:4.5, reviews:91 },
  { id:7, title:'Chicken Tikka Masala', category:'Dinner', prep:50, image:'assets/img/dishes_7.jpg', rating:4.7, reviews:150 },
  { id:8, title:'French Toast', category:'Breakfast', prep:18, image:'assets/img/dishes_8.jpg', rating:4.3, reviews:28 },
  { id:9, title:'Beef Tacos', category:'Lunch', prep:30, image:'assets/img/dishes_9.jpg', rating:4.2, reviews:74 },
  { id:10, title:'Margherita Pizza', category:'Dinner', prep:35, image:'assets/img/dishes_10.jpg', rating:4.6, reviews:212 },
  { id:11, title:'Berry Smoothie', category:'Beverage', prep:5, image:'assets/img/dishes_11.jpg', rating:4.0, reviews:19 },
  { id:12, title:'Lemon Tart', category:'Dessert', prep:60, image:'assets/img/dishes_12.jpg', rating:4.9, reviews:44 }
];

function getDishes(){
  // simulate async API with a short delay
  return new Promise(resolve=> setTimeout(()=> resolve(sampleDishes),150));
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
      $list.append(clone);
    });
  });
}
