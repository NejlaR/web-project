console.log("recipes.js loaded");

// =======================================
// RENDER RECIPE TABLE FROM sampleRecipes
// =======================================
function renderRecipes() {

    console.log("Rendering recipes...");

    let tbody = $("#recipesTable tbody");

    if (!tbody.length) {
        console.error("recipesTable NOT FOUND!");
        return;
    }

    tbody.empty(); // očisti tabelu

    sampleRecipes.forEach(recipe => {
        tbody.append(`
            <tr>
                <td>${recipe.title}</td>
                <td>${recipe.category}</td>
                <td>${recipe.prep}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editRecipe(${recipe.id})">
                        Edit
                    </button>
                </td>
            </tr>
        `);
    });
}

// funkcija za otvaranje forme za edit
function editRecipe(id) {
    window.location.href = `#recipe-form?id=${id}`;
}
