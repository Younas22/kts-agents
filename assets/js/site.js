/**
 * Khan Travel – shared page behaviour: close the language menu on outside click / Escape.
 */
(function () {
  'use strict';

  var menus = document.querySelectorAll('[data-lang-menu]');
  if (!menus.length) return;

  document.addEventListener('click', function (e) {
    menus.forEach(function (menu) {
      if (menu.open && !menu.contains(e.target)) menu.open = false;
    });
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    menus.forEach(function (menu) {
      if (menu.open) {
        menu.open = false;
        menu.querySelector('summary').focus();
      }
    });
  });
})();
