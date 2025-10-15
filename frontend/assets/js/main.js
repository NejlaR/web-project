// main.js - minimal SPA loader and simple navigation
const views = {
  dashboard: 'views/dashboard.html',
  recipes: 'views/recipes.html',
  'recipe-form': 'views/recipe-form.html',
  ingredients: 'views/ingredients.html',
  categories: 'views/categories.html',
  profile: 'views/profile.html',
  dishes: 'views/dishes.html',
  login: 'views/login.html',
  register: 'views/register.html'
};

function loadView(name) {
  const url = views[name];
  if (!url) return console.error('View not found', name);
  $('#viewContainer').fadeOut(100, function() {
    $.get(url).done(function(html){
      $('#viewContainer').html(html).fadeIn(150);
      if (name === 'dashboard') initDashboard();
      if (name === 'recipes') initRecipes();
        if (name === 'dishes') initDishes();
          if (name === 'ingredients') initIngredients();
            if (name === 'categories') initCategories();
              if (name === 'profile') initProfile();
      // init other view-specific JS here
    }).fail(function(){
      $('#viewContainer').html('<div class="alert alert-danger">Failed to load view.</div>').fadeIn(150);
    });
  });
}

function initCategories(){
  if (typeof getCategories !== 'function') return;
  getCategories().then(list=>{
    const grid = $('#categoriesGrid'); grid.empty();
    const tpl = document.getElementById('categoryCardTpl');
    list.forEach(c=>{
      const frag = tpl.content.cloneNode(true);
      const card = frag.querySelector('.category-card');
      if (card){
        const img = card.querySelector('.category-img'); if (img) img.src = c.image;
        card.querySelector('.cat-name').textContent = c.name;
        const badge = card.querySelector('.cat-count'); if (badge) { badge.textContent = c.count; badge.style.background = c.color; }
        const overlay = card.querySelector('.category-overlay'); if (overlay) overlay.style.background = `linear-gradient(180deg, rgba(0,0,0,0) 0%, ${hexToRgba(c.color,0.35)} 100%)`;
        card.addEventListener('click', ()=> loadView('recipes'));
      }
      grid.append(frag);
    });
  });
}

// tiny helper: convert hex to rgba string
function hexToRgba(hex, alpha){
  const h = hex.replace('#','');
  const bigint = parseInt(h,16);
  const r = (bigint >> 16) & 255;
  const g = (bigint >> 8) & 255;
  const b = bigint & 255;
  return `rgba(${r},${g},${b},${alpha})`;
}

function initProfile(){
  if (typeof getProfile !== 'function') return;
  getProfile().then(p=>{
    $('#profileAvatar').attr('src', p.avatar);
    $('#profileName').text(p.name);
    $('#profileRole').text(p.role + ' • ' + p.email);
    $('#profileBio').text(p.bio);

    $('#btnEditProfile').off('click').on('click', ()=>{
      // populate modal fields
      $('#editAvatarPreview').attr('src', p.avatar);
      $('#editName').val(p.name);
      $('#editEmail').val(p.email);
      $('#editBio').val(p.bio);
      const modalEl = document.getElementById('editProfileModal');
      const modal = new bootstrap.Modal(modalEl);
      modal.show();

      // avatar preview handler
      $('#editAvatarInput').off('change').on('change', function(){
        const file = this.files && this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e){ $('#editAvatarPreview').attr('src', e.target.result); };
        reader.readAsDataURL(file);
      });

      // save handler
      $('#saveProfileBtn').off('click').on('click', function(){
        const updated = {
          id: p.id,
          name: $('#editName').val() || p.name,
          email: $('#editEmail').val() || p.email,
          role: p.role,
          avatar: $('#editAvatarPreview').attr('src') || p.avatar,
          bio: $('#editBio').val() || p.bio
        };
        if (typeof saveProfile === 'function') saveProfile(updated);
        // update UI
        $('#profileAvatar').attr('src', updated.avatar);
        $('#profileName').text(updated.name);
        $('#profileRole').text(updated.role + ' • ' + updated.email);
        $('#profileBio').text(updated.bio);
        bootstrap.Modal.getInstance(modalEl).hide();
      });
    });
  });
  // load extras (stats + recent)
  if (typeof getProfileExtras === 'function'){
    getProfileExtras().then(ex=>{
      countUp('#statRecipes', ex.stats.recipes);
      countUp('#statFollowers', ex.stats.followers);
      countUp('#statFollowing', ex.stats.following);
      const recent = $('#profileRecent'); recent.empty();
      ex.recent.forEach(r=> recent.append(`<li><strong>${r.title}</strong> <div class="small text-muted">${r.date}</div></li>`));
    });
  }
}

// simple count-up animation
function countUp(selector, end){
  const el = document.querySelector(selector);
  if (!el) return;
  let start = 0;
  const duration = 800;
  const stepTime = Math.max(20, Math.floor(duration / end));
  const timer = setInterval(()=>{
    start += Math.ceil(end / (duration / stepTime));
    if (start >= end){ start = end; clearInterval(timer); }
    el.textContent = start;
  }, stepTime);
}

$(function(){
  // delegate nav clicks
  $(document).on('click','[data-link]', function(e){
    e.preventDefault();
    const target = $(this).data('link');
    loadView(target);
  });
  // load initial view
  loadView('dashboard');
});

// Example initializers
function initDashboard(){
  // load dashboard data and render metrics + chart
  if (typeof getDashboardData === 'function'){
    getDashboardData().then(data=>{
      $('#metricRecipes').text(data.metrics.totalRecipes);
      $('#metricUsers').text(data.metrics.totalUsers);
      $('#metricRating').text(data.metrics.avgRating.toFixed(1));
      $('#metricViews').text(data.metrics.todaysViews);

      const recent = $('#recentList'); recent.empty();
      data.recent.forEach(r=> recent.append(`<li><strong>${r.title}</strong><div class="metric-small">${r.category}</div></li>`));

      if (typeof Highcharts !== 'undefined'){
        Highcharts.chart('chartContainer',{
          chart:{type:'area'},
          title:{text:'Daily Views (last 7 days)'},
          xAxis:{categories:['Mon','Tue','Wed','Thu','Fri','Sat','Sun']},
          yAxis:{title:{text:'Views'}},
          series:[{name:'Views',data:[120,200,150,300,240,360,400],color:{linearGradient:{x1:0,y1:0,x2:0,y2:1},stops:[[0,'#007bff'],[1,'#66b2ff']]}}],
          credits:{enabled:false}
        });
      }
    });
  }
}

function initRecipes(){
  // initialize datatable after content loads
  setTimeout(()=>{
    if ($.fn.DataTable) $('#recipesTable').DataTable();
  }, 200);
}

function renderStars(container, rating){
  const full = Math.floor(rating);
  const half = rating - full >= 0.5;
  let html = '';
  for (let i=0;i<full;i++) html += '<i class="bi bi-star-fill"></i>';
  if (half) html += '<i class="bi bi-star-half"></i>';
  const empty = 5 - full - (half?1:0);
  for (let i=0;i<empty;i++) html += '<i class="bi bi-star"></i>';
  html += ` <small class="text-muted">${rating.toFixed(1)}</small>`;
  container.html(html);
}

function initDishes(){
  // load sample dishes and render cards
  getDishes().then(list=>{
    const container = $('#dishesList');
    container.empty();
    const tpl = document.getElementById('dishCardTpl');
    list.forEach(d=>{
      const frag = tpl.content.cloneNode(true);
      const card = frag.querySelector('.card');
      if (card){
        const img = card.querySelector('.dish-img'); if (img) { img.src = d.image; img.alt = d.title; }
        const title = card.querySelector('.dish-title'); if (title) title.textContent = d.title;
        const cat = card.querySelector('.dish-cat'); if (cat) cat.textContent = d.category + ' • ' + d.prep + ' min';
        const ratingDiv = $(card).find('.rating');
        renderStars(ratingDiv, d.rating);
      }
      container.append(frag);
    });
  });
}

function initIngredients(){
  // populate ingredients table
  getIngredients().then(list=>{
    const table = $('#ingredientsTable');
    const tbody = table.find('tbody');
    tbody.empty();
    list.forEach(i=>{
      const row = $(`<tr>
        <td>${i.name}</td>
        <td>${i.unit}</td>
        <td>${i.stock}</td>
        <td>${i.notes}</td>
        <td><button class="btn btn-sm btn-primary btn-view-ing" data-id="${i.id}">View</button></td>
      </tr>`);
      tbody.append(row);
    });
    if ($.fn.DataTable) {
      if ($.fn.dataTable.isDataTable('#ingredientsTable')) $('#ingredientsTable').DataTable().destroy();
      $('#ingredientsTable').DataTable();
    }
    
    // View button handler
    
    $(document).off('click', '.btn-view-ing').on('click', '.btn-view-ing', function(){
    
      const id = Number($(this).data('id'));
      const item = list.find(x=>x.id===id);
      if (item){
        $('#ingredientImg').attr('src', item.image);
        $('#ingName').text(item.name);
        $('#ingStock').text(item.stock + ' ' + item.unit);
        $('#ingUnit').text(item.unit);
        $('#ingNotes').text(item.notes);
        const modal = new bootstrap.Modal(document.getElementById('ingredientModal'));
        modal.show();
      }
    });
    
    // New ingredient (placeholder)
    $('#btnNewIngredient').off('click').on('click', ()=>{
      alert('Add Ingredient form would open here.');
    });
  });
}
