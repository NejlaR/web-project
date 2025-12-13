// Sample ingredients data
let ingredientsData = [
  { id:1, name:'Flour', unit:'kg', stock:12, image:'assets/img/ingredient_3.jpg', notes:'All-purpose flour' },
  { id:2, name:'Sugar', unit:'kg', stock:6, image:'assets/img/ingredient_6.jpg', notes:'Granulated' },
  { id:3, name:'Eggs', unit:'pcs', stock:30, image:'assets/img/ingredient_2.jpg', notes:'Free-range' },
  { id:4, name:'Butter', unit:'g', stock:800, image:'assets/img/ingredient_1.jpg', notes:'Unsalted' },
  { id:5, name:'Salt', unit:'g', stock:2000, image:'assets/img/ingredient_5.jpg', notes:'Sea salt' },
  { id:6, name:'Olive Oil', unit:'ml', stock:1500, image:'assets/img/ingredient_4.jpg', notes:'Extra virgin' }
];

// Fake API
function getIngredients() {
  return new Promise(resolve => setTimeout(() => resolve(ingredientsData), 50));
}

// Render table
function renderIngredients() {
  getIngredients().then(ingredients => {
    const $tbody = $("#ingredientsTable tbody");
    $tbody.empty();

    ingredients.forEach(item => {
      const row = `
        <tr data-id="${item.id}">
          <td class="text-center">
            <img src="${item.image}" alt="${item.name}"
                 data-id="${item.id}"
                 class="img-thumbnail rounded ingredient-img">
          </td>
          <td>${item.name}</td>
          <td>${item.unit}</td>
          <td>${item.stock}</td>
          <td>${item.notes}</td>
          <td>
            <button class="btn btn-sm btn-primary btn-edit" data-id="${item.id}">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>`;
      $tbody.append(row);
    });

    attachEvents();
  });
}

// Attach events AFTER rendering table
function attachEvents() {

  $(".ingredient-img").off().on("click", function () {
    const id = $(this).data("id");
    const item = ingredientsData.find(i => i.id === id);

    $("#ingredientImg").attr("src", item.image);
    $("#ingName").text(item.name);
    $("#ingStock").text(item.stock);
    $("#ingUnit").text(item.unit);
    $("#ingNotes").text(item.notes);

    $("#ingredientModal").modal("show");

    $("#openEditFromDetail").off().on("click", function () {
      openEditModal(item);
      $("#ingredientModal").modal("hide");
    });
  });

  $(".btn-edit").off().on("click", function () {
    const id = $(this).data("id");
    const item = ingredientsData.find(i => i.id === id);
    openEditModal(item);
  });

  $(".btn-delete").off().on("click", function () {
    const id = $(this).data("id");

    if (confirm("Are you sure?")) {
      ingredientsData = ingredientsData.filter(i => i.id !== id);
      renderIngredients();
    }
  });
}

// Open modal
function openEditModal(item = null) {
  if (item) {
    $("#editId").val(item.id);
    $("#editName").val(item.name);
    $("#editUnit").val(item.unit);
    $("#editStock").val(item.stock);
    $("#editNotes").val(item.notes);
  } else {
    $("#editId").val("");
    $("#editName").val("");
    $("#editUnit").val("");
    $("#editStock").val("");
    $("#editNotes").val("");
  }

  $("#editIngredientModal").modal("show");
}

$(document).ready(() => {
  renderIngredients();

  // NEW INGREDIENT (delegated)
  $(document).on("click", "#btnNewIngredient", function () {
    openEditModal(null);
  });

  // SAVE INGREDIENT (delegated)
  $(document).on("click", "#saveIngredientBtn", function () {
    const id = $("#editId").val();
    const name = $("#editName").val();
    const unit = $("#editUnit").val();
    const stock = parseInt($("#editStock").val());
    const notes = $("#editNotes").val();

    if (id) {
      const item = ingredientsData.find(i => i.id == id);
      item.name = name;
      item.unit = unit;
      item.stock = stock;
      item.notes = notes;
    } else {
      const newId = ingredientsData.length
        ? Math.max(...ingredientsData.map(i => i.id)) + 1
        : 1;

      ingredientsData.push({
        id: newId,
        name,
        unit,
        stock,
        notes,
        image: "../assets/img/default.jpg"
      });
    }

    $("#editIngredientModal").modal("hide");
    renderIngredients();
  });

});
