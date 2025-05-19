document.addEventListener("DOMContentLoaded", () => {
    // Éléments du formulaire
    const loginForm = document.getElementById("loginForm")
    const usernameInput = document.getElementById("username")
    const passwordInput = document.getElementById("password")
    const usernameError = document.getElementById("usernameError")
    const passwordError = document.getElementById("passwordError")
    const togglePasswordBtn = document.querySelector(".toggle-password")
    const loginBtn = document.querySelector(".btn-login")
  
    // Fonction pour valider le formulaire côté client
    function validateForm() {
      let isValid = true
  
      // Réinitialiser les messages d'erreur
      usernameError.textContent = ""
      passwordError.textContent = ""
  
      // Valider le nom d'utilisateur
      if (!usernameInput.value.trim()) {
        usernameError.textContent = "Le nom d'utilisateur est requis"
        isValid = false
        usernameInput.focus()
      }
  
      // Valider le mot de passe
      if (!passwordInput.value.trim()) {
        passwordError.textContent = "Le mot de passe est requis"
        isValid = false
        if (usernameInput.value.trim()) {
          passwordInput.focus()
        }
      }
  
      return isValid
    }
  
    // Gestionnaire d'événement pour la soumission du formulaire
    if (loginForm) {
      loginForm.addEventListener("submit", (e) => {
        // Valider le formulaire avant la soumission
        if (!validateForm()) {
          e.preventDefault()
          return false
        }
  
        // Ajouter l'état de chargement au bouton
        loginBtn.classList.add("loading")
  
        // La soumission continue normalement si la validation réussit
        return true
      })
    }
  
    // Gestionnaire d'événement pour afficher/masquer le mot de passe
    if (togglePasswordBtn) {
      togglePasswordBtn.addEventListener("click", function () {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password"
        passwordInput.setAttribute("type", type)
  
        // Changer l'icône
        const icon = this.querySelector("i")
        if (type === "password") {
          icon.classList.remove("bx-show")
          icon.classList.add("bx-hide")
        } else {
          icon.classList.remove("bx-hide")
          icon.classList.add("bx-show")
        }
      })
    }
  
    // Effacer les messages d'erreur lors de la saisie
    usernameInput.addEventListener("input", () => {
      usernameError.textContent = ""
    })
  
    passwordInput.addEventListener("input", () => {
      passwordError.textContent = ""
    })
  
    // Ajouter des effets visuels aux champs de formulaire
    const inputs = document.querySelectorAll("input")
    inputs.forEach((input) => {
      // Effet au focus
      input.addEventListener("focus", function () {
        this.parentElement.style.transform = "scale(1.02)"
        this.parentElement.style.transition = "transform 0.3s ease"
      })
  
      // Retour à la normale au blur
      input.addEventListener("blur", function () {
        this.parentElement.style.transform = "scale(1)"
      })
    })
  
    // Animation d'entrée pour le conteneur de connexion
    const loginContainer = document.getElementById("login-container")
    if (loginContainer) {
      loginContainer.style.opacity = "0"
      loginContainer.style.transform = "translateY(20px)"
  
      setTimeout(() => {
        loginContainer.style.opacity = "1"
        loginContainer.style.transform = "translateY(0)"
        loginContainer.style.transition = "opacity 0.5s ease, transform 0.5s ease"
      }, 100)
    }
  })
  