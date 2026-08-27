// Interactions generales : menu mobile, sidebar back-office,
// menu utilisateur, fermeture automatique des messages flash.
document.addEventListener('DOMContentLoaded', function () {
  var header = document.querySelector('.site-header');
  var navToggle = document.querySelector('.nav-toggle');
  if (header && navToggle) {
    navToggle.addEventListener('click', function () {
      header.classList.toggle('is-open');
    });
  }

  var backShell = document.querySelector('.back-shell');
  var sidebarToggle = document.querySelector('.sidebar-toggle');
  if (backShell && sidebarToggle) {
    sidebarToggle.addEventListener('click', function () {
      backShell.classList.toggle('is-sidebar-open');
    });
  }

  document.querySelectorAll('.user-menu').forEach(function (menu) {
    var chip = menu.querySelector('.user-chip');
    if (!chip) return;
    chip.addEventListener('click', function (event) {
      event.stopPropagation();
      var wasOpen = menu.classList.contains('is-open');
      document.querySelectorAll('.user-menu.is-open').forEach(function (m) { m.classList.remove('is-open'); });
      if (!wasOpen) menu.classList.add('is-open');
    });
  });
  document.addEventListener('click', function () {
    document.querySelectorAll('.user-menu.is-open').forEach(function (m) { m.classList.remove('is-open'); });
  });

  document.querySelectorAll('[data-flash]').forEach(function (flash) {
    setTimeout(function () {
      flash.style.transition = 'opacity .4s ease';
      flash.style.opacity = '0';
      setTimeout(function () { flash.remove(); }, 400);
    }, 5000);
  });

  document.querySelectorAll('form[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (event) {
      var message = el.getAttribute('data-confirm') || 'Confirmer cette action ?';
      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });
  });

  // Meme confirmation pour les liens "supprimer" (requete GET simple).
  document.querySelectorAll('a[data-confirm]').forEach(function (el) {
    el.addEventListener('click', function (event) {
      var message = el.getAttribute('data-confirm') || 'Confirmer cette action ?';
      if (!window.confirm(message)) {
        event.preventDefault();
      }
    });
  });
});
