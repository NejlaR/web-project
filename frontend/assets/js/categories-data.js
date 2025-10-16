// === Sample categories data ===
const sampleCategories = [
  { id: 1, name: 'Breakfast', count: 12, image: 'assets/img/category_1.jpg', color: '#FF7A7A' },
  { id: 2, name: 'Lunch', count: 25, image: 'assets/img/category_2.jpg', color: '#FFB86B' },
  { id: 3, name: 'Dinner', count: 48, image: 'assets/img/category_3.jpg', color: '#6BCB77' },
  { id: 4, name: 'Dessert', count: 18, image: 'assets/img/category_4.jpg', color: '#7A9BFF' },
  { id: 5, name: 'Beverage', count: 7, image: 'assets/img/category_5.jpg', color: '#FFD36B' }
];

// Simulacija API poziva
function getCategories() {
  return new Promise(resolve => setTimeout(() => resolve(sampleCategories), 120));
}

// === Render kategorija ===
function renderCategories() {
  getCategories().then(categories => {
    const $list = $("#categoriesList");
    $list.empty();

    categories.forEach(cat => {
      const card = `
        <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
          <div class="card h-100 shadow-sm border-0 category-card"
               style="cursor:pointer; background-color:${cat.color}20;"
               data-id="${cat.id}">
            <img src="${cat.image}" class="card-img-top rounded-top" alt="${cat.name}"
                 style="height:160px; object-fit:cover;">
            <div class="card-body text-center">
              <h5 class="card-title mb-1">${cat.name}</h5>
              <p class="text-muted mb-0">${cat.count} recipes</p>
            </div>
          </div>
        </div>`;
      $list.append(card);
    });

    // Klik na karticu otvara modal
    $(".category-card").on("click", function() {
      const id = $(this).data("id");
      const cat = categories.find(c => c.id === id);

      $("#catImage").attr("src", cat.image);
      $("#catName").text(cat.name);
      $("#catCount").text(`${cat.count} recipes`);
      $("#categoryModal").modal("show");
    });
  });
}