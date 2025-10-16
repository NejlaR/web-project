const PROFILE_KEY = 'rm_profile_v1';

const defaultProfile = {
  id: 1,
  name: 'Nejla Chef',
  email: 'nejla@example.com',
  role: 'Admin',
  avatar: 'assets/img/avatar.png',
  bio: 'Passionate home cook who loves sharing recipes.'
};

const defaultProfileExtras = {
  stats: { recipes: 24, followers: 532, following: 48 },
  recent: [
    { id: 1, title: 'Chocolate Cake', date: '2025-10-10' },
    { id: 2, title: 'Lemon Tart', date: '2025-10-08' },
    { id: 3, title: 'Avocado Toast', date: '2025-09-28' }
  ]
};

function loadProfile() {
  try {
    const raw = localStorage.getItem(PROFILE_KEY);
    if (raw) return JSON.parse(raw);
  } catch (e) {}
  return defaultProfile;
}

function saveProfile(profile) {
  try {
    localStorage.setItem(PROFILE_KEY, JSON.stringify(profile));
    return true;
  } catch (e) {
    return false;
  }
}

function loadProfileExtras() {
  try {
    const raw = localStorage.getItem(PROFILE_KEY + '_extras');
    if (raw) return JSON.parse(raw);
  } catch (e) {}
  return defaultProfileExtras;
}

function saveProfileExtras(extras) {
  try {
    localStorage.setItem(PROFILE_KEY + '_extras', JSON.stringify(extras));
    return true;
  } catch (e) {
    return false;
  }
}

function getProfile() {
  return new Promise(resolve => setTimeout(() => resolve(loadProfile()), 120));
}

function getProfileExtras() {
  return new Promise(resolve => setTimeout(() => resolve(loadProfileExtras()), 120));
}

// =============================
// === PROFILE RENDER LOGIC ===
// =============================

function renderProfile() {
  Promise.all([getProfile(), getProfileExtras()]).then(([profile, extras]) => {

    // --- Osnovni podaci ---
    $("#profileAvatar").attr("src", profile.avatar || "assets/img/avatar.png");
    $("#profileName").text(profile.name);
    $("#profileRole").text(profile.role);
    $("#profileBio").text(profile.bio);

    // --- Statistika ---
    $("#statRecipes").text(extras.stats.recipes);
    $("#statFollowers").text(extras.stats.followers);
    $("#statFollowing").text(extras.stats.following);

    // --- Nedavna aktivnost ---
    const $recent = $("#profileRecent");
    $recent.empty();
    extras.recent.forEach(r => {
      $recent.append(`
        <li class="mb-2">
          <i class="bi bi-journal-text text-primary me-2"></i>
          <strong>${r.title}</strong>
          <small class="text-muted">(${r.date})</small>
        </li>
      `);
    });

    // --- Uredi profil ---
    $("#btnEditProfile").off("click").on("click", function () {
      $("#editName").val(profile.name);
      $("#editEmail").val(profile.email);
      $("#editBio").val(profile.bio);
      $("#editAvatarPreview").attr("src", profile.avatar || "assets/img/avatar.png");
      $("#editProfileModal").modal("show");
    });

    // --- Promjena avatara ---
    $("#editAvatarInput").off("change").on("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
          $("#editAvatarPreview").attr("src", ev.target.result);
        };
        reader.readAsDataURL(file);
      }
    });

    // --- Spremi profil ---
    $("#saveProfileBtn").off("click").on("click", function () {
      const updated = {
        ...profile,
        name: $("#editName").val(),
        email: $("#editEmail").val(),
        bio: $("#editBio").val(),
        avatar: $("#editAvatarPreview").attr("src")
      };
      saveProfile(updated);
      $("#editProfileModal").modal("hide");
      renderProfile();
    });
  });
}