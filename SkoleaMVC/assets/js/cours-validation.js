// Validation du formulaire "Creer / Modifier un cours".
// Reprend les 3 techniques vues en cours sur un seul formulaire :
// 1) onClick + alert()  2) addEventListener('submit') avec messages rouge/vert
// 3) un evenement different par champ (keyup, blur, change).

// --- Technique 1 : onClick sur le bouton, erreurs dans une alert() ---
function validerFormulaire() {
  var titre = document.getElementById('titre').value.trim();
  var description = document.getElementById('description').value.trim();
  var categorie = document.getElementById('categorie_id').value;
  var erreurs = [];

  if (titre.length < 3) erreurs.push('Le titre doit contenir au moins 3 caracteres.');
  if (description === '') erreurs.push('La description est obligatoire.');
  if (categorie === '') erreurs.push('Merci de choisir une categorie.');

  if (erreurs.length > 0) {
    alert(erreurs.join('\n'));
    return false;
  }
  return true;
}

(function () {
  var form = document.getElementById('form-cours');
  if (!form) return;

  function afficherMessage(champId, correct, texte) {
    var message = document.getElementById(champId + '-message');
    if (!message) return;
    message.textContent = texte;
    message.className = correct ? 'form-success' : 'form-error';
  }

  function verifierTitre() {
    var correct = document.getElementById('titre').value.trim().length >= 3;
    afficherMessage('titre', correct, correct ? 'Correct' : 'Le titre doit contenir au moins 3 caracteres.');
    return correct;
  }

  function verifierDescription() {
    var correct = document.getElementById('description').value.trim() !== '';
    afficherMessage('description', correct, correct ? 'Correct' : 'La description est obligatoire.');
    return correct;
  }

  function verifierCategorie() {
    var correct = document.getElementById('categorie_id').value !== '';
    afficherMessage('categorie_id', correct, correct ? 'Correct' : 'Merci de choisir une categorie.');
    return correct;
  }

  // --- Technique 2 : validation complete a la soumission, messages rouge/vert ---
  form.addEventListener('submit', function (event) {
    var titreOk = verifierTitre();
    var descriptionOk = verifierDescription();
    var categorieOk = verifierCategorie();

    if (!titreOk || !descriptionOk || !categorieOk) {
      event.preventDefault();
    }
  });

  // --- Technique 3 : un evenement JS different par champ ---
  document.getElementById('titre').addEventListener('keyup', verifierTitre);
  document.getElementById('description').addEventListener('blur', verifierDescription);
  document.getElementById('categorie_id').addEventListener('change', verifierCategorie);

  document.getElementById('statut').addEventListener('change', function () {
    var message = document.getElementById('statut-message');
    if (this.value === 'publie') {
      message.textContent = 'Cours publie : visible par tous les etudiants.';
      message.className = 'form-success';
    } else {
      message.textContent = "Cours en brouillon : invisible pour les etudiants.";
      message.className = 'form-hint';
    }
  });
})();
