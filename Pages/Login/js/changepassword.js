document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si le mot de passe a été changé avec succès
    if (passwordChanged) {
        showSuccessAlert();
    }

    // Éléments du formulaire
    const oldPasswordInput = document.getElementById('old_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    const strengthMeterFill = document.getElementById('strength-meter-fill');
    const strengthText = document.getElementById('strength-text');
    const cancelBtn = document.getElementById('cancel-btn');

    // Éléments pour les exigences de mot de passe
    const lengthRequirement = document.getElementById('length-requirement');
    const uppercaseRequirement = document.getElementById('uppercase-requirement');
    const lowercaseRequirement = document.getElementById('lowercase-requirement');
    const numberRequirement = document.getElementById('number-requirement');
    const specialRequirement = document.getElementById('special-requirement');
    
    // Fonction pour basculer la visibilité du mot de passe
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Changer l'icône
            const icon = this.querySelector('i');
            if (type === 'password') {
                icon.classList.remove('bx-show');
                icon.classList.add('bx-hide');
            } else {
                icon.classList.remove('bx-hide');
                icon.classList.add('bx-show');
            }
        });
    });
    
    // Fonction pour vérifier la force du mot de passe
    function checkPasswordStrength(password) {
        let strength = 0;
        
        // Vérifier la longueur
        if (password.length >= 8) {
            strength += 1;
            updateRequirement(lengthRequirement, true);
        } else {
            updateRequirement(lengthRequirement, false);
        }
        
        // Vérifier les majuscules
        if (/[A-Z]/.test(password)) {
            strength += 1;
            updateRequirement(uppercaseRequirement, true);
        } else {
            updateRequirement(uppercaseRequirement, false);
        }
        
        // Vérifier les minuscules
        if (/[a-z]/.test(password)) {
            strength += 1;
            updateRequirement(lowercaseRequirement, true);
        } else {
            updateRequirement(lowercaseRequirement, false);
        }
        
        // Vérifier les chiffres
        if (/[0-9]/.test(password)) {
            strength += 1;
            updateRequirement(numberRequirement, true);
        } else {
            updateRequirement(numberRequirement, false);
        }
        
        // Vérifier les caractères spéciaux
        if (/[^A-Za-z0-9]/.test(password)) {
            strength += 1;
            updateRequirement(specialRequirement, true);
        } else {
            updateRequirement(specialRequirement, false);
        }
        
        return strength;
    }
    
    // Fonction pour mettre à jour l'affichage des exigences
    function updateRequirement(element, isValid) {
        const icon = element.querySelector('i');
        
        if (isValid) {
            icon.className = 'bx bx-check-circle requirement-valid';
            element.classList.add('requirement-valid');
            element.classList.remove('requirement-invalid');
        } else {
            icon.className = 'bx bx-x-circle requirement-invalid';
            element.classList.add('requirement-invalid');
            element.classList.remove('requirement-valid');
        }
    }
    
    // Écouter les changements dans le champ de nouveau mot de passe
    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        
        // Mettre à jour l'indicateur de force
        strengthMeterFill.className = 'strength-meter-fill';
        
        if (password === '') {
            strengthText.textContent = 'Force du mot de passe';
            strengthText.style.color = 'var(--gray-600)';
            strengthMeterFill.style.width = '0';
        } else if (strength < 3) {
            strengthText.textContent = 'Faible';
            strengthText.style.color = 'var(--danger)';
            strengthMeterFill.classList.add('weak');
        } else if (strength < 5) {
            strengthText.textContent = 'Moyen';
            strengthText.style.color = 'var(--warning)';
            strengthMeterFill.classList.add('medium');
        } else {
            strengthText.textContent = 'Fort';
            strengthText.style.color = 'var(--success)';
            strengthMeterFill.classList.add('strong');
        }
        
        // Vérifier si les mots de passe correspondent
        checkPasswordsMatch();
    });
    
    // Vérifier si les mots de passe correspondent
    function checkPasswordsMatch() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword === '') return;
        
        const errorMessage = document.querySelector('#confirm_password').nextElementSibling.nextElementSibling;
        
        if (newPassword === confirmPassword) {
            errorMessage.textContent = '';
        } else {
            errorMessage.textContent = 'Les mots de passe ne correspondent pas';
        }
    }
    
    // Écouter les changements dans le champ de confirmation
    confirmPasswordInput.addEventListener('input', checkPasswordsMatch);
    
    // Effet de focus sur les champs
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.3s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Gérer le bouton d'annulation
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous devez changer votre mot de passe pour continuer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, annuler',
                cancelButtonText: 'Non, continuer'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        });
    }

    // Animation d'entrée pour le formulaire
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(10px)';
        setTimeout(() => {
            group.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            group.style.opacity = '1';
            group.style.transform = 'translateY(0)';
        }, 100 + (index * 100));
    });
});

/**
 * Affiche une alerte de succès et redirige vers la page des garanties
 */
function showSuccessAlert() {
    Swal.fire({
        icon: 'success',
        title: 'Succès!',
        text: 'Votre mot de passe a été changé avec succès.',
        showConfirmButton: false,
        timer: 2000
    }).then(function() {
        window.location.href = '../Role/admindash.php';
    });
}