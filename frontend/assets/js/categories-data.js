// ==========================================
//   CATEGORY SERVICE (TVOJ, NE MIJENJAN)
// ==========================================

let CategoryService = {

  getAll: function(callback){
      RestClient.get("categories", res => callback(res.data || res));
  },

  getById: function(id, callback){
      RestClient.get("categories/" + id, res => callback(res.data || res));
  },

  search: function(term, callback){
      RestClient.get("categories/search/" + term, res => callback(res.data || res));
  },

  getOrdered: function(callback){
      RestClient.get("categories/ordered", res => callback(res.data || res));
  },

  getWithCount: function(callback){
      RestClient.get("categories/with-count", res => callback(res.data || res));
  },

  add: function(category, callback){
      RestClient.post("categories", category, callback);
  },

  update: function(id, category, callback){
      RestClient.put("categories/" + id, category, callback);
  },

  delete: function(id, callback){
      RestClient.delete("categories/" + id, callback);
  }
};


// ==========================================
//   RENDER CATEGORIES (API + SLIKE + BROJEVI)
// ==========================================

function renderCategories() {

  const $list = $("#categoriesList");
  $list.empty();

  CategoryService.getWithCount(function (categories) {

    // sigurnosna provjera
    if (!Array.isArray(categories)) {
      console.error("Expected array, got:", categories);
      return;
    }

    categories.forEach(cat => {

      $list.append(`
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
          <div class="card h-100 shadow-sm border-0 category-card"
               style="cursor:pointer;"
               data-id="${cat.id}">
            <img src="${cat.image || 'assets/img/default_category.jpg'}"
                 class="card-img-top rounded-top"
                 style="height:160px; object-fit:cover;">
            <div class="card-body text-center">
              <h5 class="card-title mb-1">${cat.name}</h5>
              <p class="text-muted mb-0">${cat.recipe_count || 0} recipes</p>
            </div>
          </div>
        </div>
      `);

    });

  });
}


// ==========================================
//   OPEN ADD CATEGORY MODAL (ADMIN)
// ==========================================

$(document).on("click", "#btnNewCategory", function () {

  $("#newCatName").val("");
  $("#newCatCount").val("");
  $("#newCatImage").val("");

  $("#addCategoryModal").modal("show");
});


// ==========================================
//   SAVE CATEGORY (API – ADMIN ONLY)
// ==========================================

$(document).on("click", "#saveCategoryBtn", function () {

  const name = $("#newCatName").val().trim();
  const count = parseInt($("#newCatCount").val());
  let image = $("#newCatImage").val().trim();

  if (!name || isNaN(count)) {
    alert("Please fill all fields!");
    return;
  }

  if (!image) {
    image = "assets/img/default_category.jpg";
  }

  CategoryService.add(
    { name, count, image },
    function () {
      $("#addCategoryModal").modal("hide");
      renderCategories();
    }
  );
});


// ==========================================
//   CATEGORY CARD CLICK (LOAD FROM API)
// ==========================================

$(document).on("click", ".category-card", function () {

  const id = $(this).data("id");

  CategoryService.getById(id, function (cat) {

    $("#catImage").attr("src", cat.image || "assets/img/default_category.jpg");
    $("#catName").text(cat.name);
    $("#catCount").text((cat.recipe_count || 0) + " recipes");

    $("#categoryModal").modal("show");
  });
});
