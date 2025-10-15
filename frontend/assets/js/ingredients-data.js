// Sample ingredients data
const sampleIngredients = [
  { id:1, name:'Flour', unit:'kg', stock:12, image:'assets/img/ingredient_3.jpg', notes:'All-purpose flour' },
  { id:2, name:'Sugar', unit:'kg', stock:6, image:'assets/img/ingredient_6.jpg', notes:'Granulated' },
  { id:3, name:'Eggs', unit:'pcs', stock:30, image:'assets/img/ingredient_2.jpg', notes:'Free-range' },
  { id:4, name:'Butter', unit:'g', stock:800, image:'assets/img/ingredient_1.jpg', notes:'Unsalted' },
  { id:5, name:'Salt', unit:'g', stock:2000, image:'assets/img/ingredient_5.jpg', notes:'Sea salt' },
  { id:6, name:'Olive Oil', unit:'ml', stock:1500, image:'assets/img/ingredient_4.jpg', notes:'Extra virgin' }
];

// Simulacija API poziva
function getIngredients() {
  return new Promise(resolve => setTimeout(() => resolve(sampleIngredients), 100));
}

// Render tabele
function renderIngredients() {
  getIngredients().then(ingredients => {
    const $tbody = $("#ingredientsTable tbody");
    $tbody.empty();

    ingredients.forEach(item => {
      const row = `
        <tr>
          <td class="text-center">
            <img src="${item.image}" alt="${item.name}"
                 data-name="${item.name}"
                 data-unit="${item.unit}"
                 data-stock="${item.stock}"
                 data-notes="${item.notes}"
                 class="img-thumbnail rounded">
          </td>
          <td>${item.name}</td>
          <td>${item.unit}</td>
          <td>${item.stock}</td>
          <td>${item.notes}</td>
          <td>
            <button class="btn btn-sm btn-primary me-1">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>`;
      $tbody.append(row);
    });

    // Klik na sliku otvara modal
    $("#ingredientsTable img").on("click", function() {
      const img = $(this);
      $("#ingredientImg").attr("src", img.attr("src"));
      $("#ingName").text(img.data("name"));
      $("#ingStock").text(img.data("stock"));
      $("#ingUnit").text(img.data("unit"));
      $("#ingNotes").text(img.data("notes"));
      $("#ingredientModal").modal("show");
    });
  });
}
