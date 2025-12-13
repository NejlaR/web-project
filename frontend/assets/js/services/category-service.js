function renderCategories() {

  const $list = $("#categoriesList");
  $list.empty();

  CategoryService.getWithCount(function (categories) {

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
