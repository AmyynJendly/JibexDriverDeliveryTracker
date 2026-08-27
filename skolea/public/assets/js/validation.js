/**
 * Validation cote client des formulaires marques [data-validate].
 * Chaque champ declare ses regles via l'attribut data-rule
 * (ex: data-rule="required|email" ou data-rule="required|min:8").
 * Les formulaires portent l'attribut novalidate : la validation HTML5
 * native (required, pattern, ...) n'est pas utilisee, tout passe par
 * ce script (et, dans tous les cas, par une revalidation cote PHP).
 */
(function () {
  'use strict';

  var MESSAGES = {
    required: 'Ce champ est obligatoire.',
    email: 'Merci de saisir une adresse email valide.',
    numeric: 'Ce champ doit etre un nombre.',
  };

  function parseRules(field) {
    var raw = field.getAttribute('data-rule');
    if (!raw) return [];
    return raw.split('|').filter(Boolean).map(function (part) {
      var pieces = part.split(':');
      return { name: pieces[0], arg: pieces[1] };
    });
  }

  function isEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  function validateField(field, form) {
    var rules = parseRules(field);
    if (rules.length === 0) return true;

    var value = field.value.trim();
    var error = '';

    for (var i = 0; i < rules.length; i++) {
      var rule = rules[i];

      if (rule.name === 'required' && value === '') {
        error = MESSAGES.required;
        break;
      }
      if (value === '') continue;

      if (rule.name === 'email' && !isEmail(value)) {
        error = MESSAGES.email;
        break;
      }
      if (rule.name === 'numeric' && isNaN(Number(value))) {
        error = MESSAGES.numeric;
        break;
      }
      if (rule.name === 'min' && value.length < parseInt(rule.arg, 10)) {
        error = 'Ce champ doit contenir au moins ' + rule.arg + ' caracteres.';
        break;
      }
      if (rule.name === 'max' && value.length > parseInt(rule.arg, 10)) {
        error = 'Ce champ ne doit pas depasser ' + rule.arg + ' caracteres.';
        break;
      }
      if (rule.name === 'matches') {
        var other = form.querySelector('[name="' + rule.arg + '"]');
        if (other && other.value !== field.value) {
          error = 'Les deux champs ne correspondent pas.';
          break;
        }
      }
    }

    showError(field, error);
    return error === '';
  }

  function showError(field, message) {
    field.classList.toggle('is-invalid', message !== '');

    var container = field.closest('.form-group') || field.parentElement;
    var existing = container.querySelector('[data-js-error]');

    if (message === '') {
      if (existing) existing.remove();
      return;
    }

    if (!existing) {
      existing = document.createElement('p');
      existing.className = 'form-error';
      existing.setAttribute('data-js-error', '1');
      container.appendChild(existing);
    }
    existing.textContent = message;
  }

  function bindForm(form) {
    var fields = form.querySelectorAll('[data-rule]');

    fields.forEach(function (field) {
      field.addEventListener('blur', function () { validateField(field, form); });
      field.addEventListener('input', function () {
        if (field.classList.contains('is-invalid')) validateField(field, form);
      });
    });

    form.addEventListener('submit', function (event) {
      var valid = true;
      var firstInvalid = null;

      fields.forEach(function (field) {
        var ok = validateField(field, form);
        if (!ok) {
          valid = false;
          if (!firstInvalid) firstInvalid = field;
        }
      });

      if (!valid) {
        event.preventDefault();
        if (firstInvalid) firstInvalid.focus();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-validate]').forEach(bindForm);
  });
})();
