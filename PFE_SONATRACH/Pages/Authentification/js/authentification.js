
// document.addEventListener('DOMContentLoaded', function () {
//     // Initialiser les champs de date avec la date du jour
//     initializeDateFields();
    
//     // Animation des cartes au chargement
//     animateCards();
    
//     // Gestion du champ de fichier
//     setupFileInput();
    
//     // Afficher SweetAlert si nécessaire
//     if (showSweetAlert) {
//         showSuccessAlert();
//     }
    
//     // Empêcher le défilement de la page lors de la soumission du formulaire avec des erreurs
//     setupFormSubmission();
// });

// /**
//  * Initialise les champs de date avec la date du jour
//  */
// function initializeDateFields() {
//     const today = new Date().toISOString().split('T')[0];
    
//     if (document.getElementById('date_depo') && !document.getElementById('date_depo').value) {
//         document.getElementById('date_depo').value = today;
//     }
    
//     if (document.getElementById('date_auth') && !document.getElementById('date_auth').value) {
//         document.getElementById('date_auth').value = today;
//     }
// }

// /**
//  * Configure l'input de fichier pour afficher le nom du fichier sélectionné
//  */
// function setupFileInput() {
//     const fileInput = document.getElementById('document_scanne');
//     const fileSelectedName = document.getElementById('file-selected-name');
    
//     if (fileInput) {
//         fileInput.addEventListener('change', function() {
//             if (this.files.length > 0) {
//                 fileSelectedName.textContent = 'Fichier sélectionné: ' + this.files[0].name;
//                 fileSelectedName.style.display = 'block';
//                 document.getElementById('file-drop-area').style.borderColor = '#28a745';
//             } else {
//                 fileSelectedName.style.display = 'none';
//                 document.getElementById('file-drop-area').style.borderColor = '#adb5bd';
//             }
//         });
//     }
// }

// /**
//  * Affiche une alerte de succès avec SweetAlert
//  */
// function showSuccessAlert() {
//     Swal.fire({
//         title: 'Succès!',
//         text: 'Garantie authentifiée',
//         icon: 'success',
//         confirmButtonText: 'OK',
//         confirmButtonColor: '#007bff'
//     }).then((result) => {
//         // Rediriger vers la page actuelle pour afficher les informations d'authentification
//         window.location.href = currentPage + '?garantie_id=' + garantieId;
//     });
// }

// /**
//  * Configure la soumission du formulaire pour éviter le défilement
//  */
// function setupFormSubmission() {
//     const form = document.querySelector('#dynamic-content form');
    
//     if (form) {
//         form.addEventListener('submit', function (event) {
//             // La validation est gérée côté serveur
//             // Ce code empêche simplement le défilement de la page
//             if (event.submitter && event.submitter.name === 'submit_auth') {
//                 // Stocker la position de défilement actuelle
//                 const scrollPos = window.scrollY;
                
//                 // Après la soumission du formulaire, revenir à la même position
//                 setTimeout(function() {
//                     window.scrollTo(0, scrollPos);
//                 }, 0);
//             }
//         });
//     }
// }