document.addEventListener("DOMContentLoaded", () => {
    // Éléments du DOM
    const addModal = document.getElementById("addModal")
    const btnAjouter = document.getElementById("btnAjouter")
    const userForm = document.getElementById("userForm")
    const btnAnnuler = document.getElementById("btnAnnuler")
    const closeBtn = document.querySelector(".close")
    const searchInput = document.getElementById("searchInput")
    const noResultsDiv = document.getElementById("noResults")
    const paginationContainer = document.getElementById("paginationContainer")
  
    // Importation de SweetAlert2
    const Swal = window.Swal
  
    // Configuration SweetAlert2 par défaut
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer)
        toast.addEventListener("mouseleave", Swal.resumeTimer)
      },
    })
  
    // Variables de pagination
    let currentPage = 1
    const itemsPerPage = 10
    let totalItems = 0
    let allUsers = []
  
    // Initialisation
    initializeApp()
  
    /**
     * Initialise l'application
     */
    function initializeApp() {
      // Charger les utilisateurs au démarrage
      loadUsers()
  
      // Ajouter des effets visuels aux champs de formulaire
      addFormFieldEffects()
  
      // Ajouter l'effet de ripple aux boutons
      addRippleEffect()
  
      // Initialiser la recherche
      initializeSearch()
  
      // Initialiser les modales
      initializeModals()
  
      // Initialiser les formulaires
      initializeForms()
    }
  
    /**
     * Initialise la fonctionnalité de recherche
     */
    function initializeSearch() {
      if (!searchInput) return
  
      // Réinitialiser la recherche au chargement
      searchInput.value = ""
  
      // Cacher le message "aucun résultat" au départ
      if (noResultsDiv) {
        noResultsDiv.style.display = "none"
      }
  
      // Ajouter l'événement de recherche
      searchInput.addEventListener("input", function () {
        const searchTerm = this.value.trim().toLowerCase()
        if (searchTerm.length > 0) {
          searchUsers(searchTerm)
        } else {
          loadUsers() // Charger tous les utilisateurs si la recherche est vide
        }
      })
    }
  
    /**
     * Initialise les modales
     */
    function initializeModals() {
      // Événement pour ouvrir le modal d'ajout
      if (btnAjouter) {
        btnAjouter.addEventListener("click", (e) => {
          e.preventDefault()
          if (userForm) userForm.reset()
          clearErrors()
          showModal(addModal)
        })
      }
  
      // Fermer le modal avec le bouton X
      if (closeBtn) {
        closeBtn.addEventListener("click", () => {
          hideModal(addModal)
        })
      }
  
      // Fermer le modal avec le bouton Annuler
      if (btnAnnuler) {
        btnAnnuler.addEventListener("click", (e) => {
          e.preventDefault()
          hideModal(addModal)
        })
      }
  
      // Fermer le modal en cliquant en dehors
      window.addEventListener("click", (event) => {
        if (event.target === addModal) {
          hideModal(addModal)
        }
      })
    }
  
    /**
     * Initialise les formulaires
     */
    function initializeForms() {
      // Soumission du formulaire d'ajout
      if (userForm) {
        userForm.addEventListener("submit", (event) => {
          event.preventDefault()
  
          // Récupérer les valeurs du formulaire
          const nomComplet = document.getElementById("nomComplet").value.trim()
          const userName = document.getElementById("userName").value.trim()
          const compte = document.getElementById("compte").value.trim()
          // Utiliser le mot de passe par défaut
          const motDePasse = "P@ssword123"
          const structure = document.getElementById("structure").value.trim()
          const role = document.getElementById("role").value.trim()
  
          // Valider le formulaire
          if (!validateForm(nomComplet, userName, compte, structure, role)) {
            return false
          }
  
          // Afficher l'indicateur de chargement
          Swal.fire({
            title: "Ajout en cours...",
            text: "Veuillez patienter",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
              Swal.showLoading()
            },
          })
  
          // Envoyer les données au serveur
          fetch("../../Backend/GestionUsers/requetes_ajax/add_user.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              nomComplet,
              userName,
              compte,
              motDePasse,
              structure,
              role,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              // Fermer l'indicateur de chargement
              Swal.close()
  
              if (data.success) {
                // Message de succès
                Swal.fire({
                  title: "Succès!",
                  text: data.message || "Utilisateur ajouté avec succès",
                  icon: "success",
                  confirmButtonText: "OK",
                  confirmButtonColor: "#007bff",
                })
                userForm.reset()
                hideModal(addModal)
                // Recharger les utilisateurs pour afficher le nouvel utilisateur
                loadUsers()
              } else {
                // Message d'erreur
                Swal.fire({
                  title: "Erreur!",
                  text: data.message || "Une erreur est survenue lors de l'ajout de l'utilisateur",
                  icon: "error",
                  confirmButtonText: "OK",
                  confirmButtonColor: "#007bff",
                })
              }
            })
            .catch((error) => {
              console.error("Erreur:", error)
              // Message d'erreur réseau
              Swal.fire({
                title: "Erreur réseau!",
                text: "Impossible de communiquer avec le serveur",
                icon: "error",
                confirmButtonText: "OK",
                confirmButtonColor: "#007bff",
              })
            })
        })
      }
    }
  
    /**
     * Valide le formulaire
     * @returns {boolean} - True si le formulaire est valide, false sinon
     */
    function validateForm(nomComplet, userName, compte, structure, role) {
      let isValid = true
  
      // Vider les messages d'erreur précédents
      clearErrors()
  
      // Valider le nom complet
      if (!nomComplet) {
        showError("nomCompletError", "Le nom complet est obligatoire")
        isValid = false
      }
  
      // Valider le nom d'utilisateur
      if (!userName) {
        showError("userNameError", "Le nom d'utilisateur est obligatoire")
        isValid = false
      }
  
      // Valider le compte
      if (!compte) {
        showError("compteError", "Le statut est obligatoire")
        isValid = false
      }
  
      // Valider la structure
      if (!structure) {
        showError("structureError", "La structure est obligatoire")
        isValid = false
      }
  
      // Valider le rôle
      if (!role) {
        showError("roleError", "Le rôle est obligatoire")
        isValid = false
      }
  
      if (!isValid) {
        // Afficher une erreur de validation avec SweetAlert2
        Swal.fire({
          title: "Erreur de validation",
          text: "Veuillez remplir tous les champs obligatoires",
          icon: "error",
          confirmButtonText: "OK",
          confirmButtonColor: "#007bff",
        })
      }
  
      return isValid
    }
  
    /**
     * Affiche un message d'erreur
     * @param {string} elementId - L'ID de l'élément où afficher l'erreur
     * @param {string} message - Le message d'erreur à afficher
     */
    function showError(elementId, message) {
      const errorElement = document.getElementById(elementId)
      if (errorElement) {
        errorElement.textContent = message
        errorElement.style.display = "block"
        errorElement.classList.add("show")
  
        // Ajouter une animation de secousse
        const inputField = errorElement.closest(".form-group").querySelector(".form-control")
        if (inputField) {
          inputField.classList.add("error-input")
          inputField.style.animation = "shake 0.5s cubic-bezier(.36,.07,.19,.97) both"
          setTimeout(() => {
            inputField.style.animation = ""
          }, 500)
        }
      }
    }
  
    /**
     * Efface tous les messages d'erreur
     */
    function clearErrors() {
      const errorElements = document.querySelectorAll(".error")
      errorElements.forEach((element) => {
        element.textContent = ""
        element.style.display = "none"
        element.classList.remove("show")
      })
  
      const inputFields = document.querySelectorAll(".form-control")
      inputFields.forEach((field) => {
        field.classList.remove("error-input")
      })
    }
  
    /**
     * Charge tous les utilisateurs depuis le serveur
     */
    function loadUsers() {
      // Afficher un indicateur de chargement
      const usersData = document.getElementById("usersData")
      if (usersData) {
        usersData.innerHTML = `
          <li class="user-item" style="justify-content: center;">
            <div style="text-align: center;">
              <i class='bx bx-loader-alt bx-spin' style="font-size: 2rem; color: #007bff;"></i>
              <p>Chargement des utilisateurs...</p>
            </div>
          </li>
        `
      }
  
      fetch("../../Backend/GestionUsers/requetes_ajax/get_users.php")
        .then((response) => response.json())
        .then((users) => {
          // Stocker tous les utilisateurs
          allUsers = users
          totalItems = users.length
  
          // Mettre à jour la pagination
          updatePagination()
  
          // Afficher la première page
          displayPage(1)
  
          // Cacher le message "aucun résultat"
          if (noResultsDiv) {
            noResultsDiv.style.display = "none"
          }
        })
        .catch((error) => {
          console.error("Erreur de chargement des utilisateurs:", error)
  
          // Afficher un message d'erreur dans le tableau
          if (usersData) {
            usersData.innerHTML = `
              <li class="user-item" style="justify-content: center;">
                <div style="text-align: center; color: #dc3545;">
                  <i class='bx bx-error-circle' style="font-size: 2rem;"></i>
                  <p>Erreur de chargement des utilisateurs</p>
                </div>
              </li>
            `
          }
  
          // Afficher un message d'erreur
          Toast.fire({
            icon: "error",
            title: "Erreur de chargement",
            text: "Impossible de charger les utilisateurs",
          })
        })
    }
  
    /**
     * Met à jour la pagination
     */
    function updatePagination() {
      if (!paginationContainer) return
  
      const totalPages = Math.ceil(totalItems / itemsPerPage)
  
      // Créer la pagination si elle n'existe pas
      if (!document.getElementById("pagination")) {
        const paginationDiv = document.createElement("div")
        paginationDiv.className = "pagination-container"
        paginationDiv.innerHTML = `
          <div class="pagination-info">
            Affichage de <span id="startItem">1</span> à <span id="endItem">${Math.min(itemsPerPage, totalItems)}</span> sur <span id="totalItems">${totalItems}</span> utilisateurs
          </div>
          <ul class="pagination" id="pagination"></ul>
        `
        paginationContainer.innerHTML = ""
        paginationContainer.appendChild(paginationDiv)
      }
  
      const paginationElement = document.getElementById("pagination")
      const startItemElement = document.getElementById("startItem")
      const endItemElement = document.getElementById("endItem")
      const totalItemsElement = document.getElementById("totalItems")
  
      if (!paginationElement || !startItemElement || !endItemElement || !totalItemsElement) return
  
      // Afficher le conteneur de pagination s'il y a des éléments
      paginationContainer.style.display = totalItems > 0 ? "block" : "none"
  
      // Mettre à jour les informations de pagination
      const startItem = (currentPage - 1) * itemsPerPage + 1
      const endItem = Math.min(currentPage * itemsPerPage, totalItems)
  
      startItemElement.textContent = startItem
      endItemElement.textContent = endItem
      totalItemsElement.textContent = totalItems
  
      // Générer les liens de pagination
      paginationElement.innerHTML = ""
  
      // Bouton précédent
      const prevLi = document.createElement("li")
      if (currentPage > 1) {
        const prevLink = document.createElement("a")
        prevLink.href = "#"
        prevLink.innerHTML = '<i class="bx bx-chevron-left"></i>'
        prevLink.addEventListener("click", (e) => {
          e.preventDefault()
          if (currentPage > 1) {
            currentPage--
            displayPage(currentPage)
            updatePagination()
          }
        })
        prevLi.appendChild(prevLink)
      } else {
        prevLi.className = "disabled"
        const prevSpan = document.createElement("span")
        prevSpan.innerHTML = '<i class="bx bx-chevron-left"></i>'
        prevLi.appendChild(prevSpan)
      }
      paginationElement.appendChild(prevLi)
  
      // Pages
      const startPage = Math.max(1, currentPage - 2)
      const endPage = Math.min(totalPages, startPage + 4)
  
      // Première page
      if (startPage > 1) {
        const firstLi = document.createElement("li")
        const firstLink = document.createElement("a")
        firstLink.href = "#"
        firstLink.textContent = "1"
        firstLink.addEventListener("click", (e) => {
          e.preventDefault()
          currentPage = 1
          displayPage(currentPage)
          updatePagination()
        })
        firstLi.appendChild(firstLink)
        paginationElement.appendChild(firstLi)
  
        // Ellipsis
        if (startPage > 2) {
          const ellipsisLi = document.createElement("li")
          ellipsisLi.className = "disabled"
          const ellipsisSpan = document.createElement("span")
          ellipsisSpan.textContent = "..."
          ellipsisLi.appendChild(ellipsisSpan)
          paginationElement.appendChild(ellipsisLi)
        }
      }
  
      // Pages numérotées
      for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement("li")
        if (i === currentPage) {
          pageLi.className = "active"
        }
  
        const pageLink = document.createElement("a")
        pageLink.href = "#"
        pageLink.textContent = i
        pageLink.addEventListener("click", (e) => {
          e.preventDefault()
          currentPage = i
          displayPage(currentPage)
          updatePagination()
        })
  
        pageLi.appendChild(pageLink)
        paginationElement.appendChild(pageLi)
      }
  
      // Dernière page
      if (endPage < totalPages) {
        // Ellipsis
        if (endPage < totalPages - 1) {
          const ellipsisLi = document.createElement("li")
          ellipsisLi.className = "disabled"
          const ellipsisSpan = document.createElement("span")
          ellipsisSpan.textContent = "..."
          ellipsisLi.appendChild(ellipsisSpan)
          paginationElement.appendChild(ellipsisLi)
        }
  
        const lastLi = document.createElement("li")
        const lastLink = document.createElement("a")
        lastLink.href = "#"
        lastLink.textContent = totalPages
        lastLink.addEventListener("click", (e) => {
          e.preventDefault()
          currentPage = totalPages
          displayPage(currentPage)
          updatePagination()
        })
        lastLi.appendChild(lastLink)
        paginationElement.appendChild(lastLi)
      }
  
      // Bouton suivant
      const nextLi = document.createElement("li")
      if (currentPage < totalPages) {
        const nextLink = document.createElement("a")
        nextLink.href = "#"
        nextLink.innerHTML = '<i class="bx bx-chevron-right"></i>'
        nextLink.addEventListener("click", (e) => {
          e.preventDefault()
          if (currentPage < totalPages) {
            currentPage++
            displayPage(currentPage)
            updatePagination()
          }
        })
        nextLi.appendChild(nextLink)
      } else {
        nextLi.className = "disabled"
        const nextSpan = document.createElement("span")
        nextSpan.innerHTML = '<i class="bx bx-chevron-right"></i>'
        nextLi.appendChild(nextSpan)
      }
      paginationElement.appendChild(nextLi)
    }
  
    /**
     * Affiche une page spécifique d'utilisateurs
     * @param {number} page - Le numéro de page à afficher
     */
    function displayPage(page) {
      currentPage = page
      const start = (page - 1) * itemsPerPage
      const end = Math.min(start + itemsPerPage, totalItems)
      const pageUsers = allUsers.slice(start, end)
  
      updateUserTable(pageUsers)
    }
  
    /**
     * Recherche des utilisateurs
     * @param {string} query - Le terme de recherche
     */
    function searchUsers(query) {
      fetch(`../../Backend/GestionUsers/requetes_ajax/search_user.php?query=${encodeURIComponent(query)}`)
        .then((response) => response.json())
        .then((users) => {
          // Stocker les résultats de recherche
          allUsers = users
          totalItems = users.length
          currentPage = 1
  
          // Mettre à jour la pagination
          updatePagination()
  
          // Afficher la première page
          displayPage(1)
  
          // Afficher ou masquer le message "aucun résultat"
          if (noResultsDiv) {
            noResultsDiv.style.display = users.length === 0 ? "flex" : "none"
          }
        })
        .catch((error) => {
          console.error("Erreur de recherche des utilisateurs:", error)
          // Afficher un message d'erreur
          Toast.fire({
            icon: "error",
            title: "Erreur de recherche",
            text: "Impossible de rechercher des utilisateurs",
          })
        })
    }
  
    /**
     * Met à jour le tableau des utilisateurs
     * @param {Array} users - La liste des utilisateurs
     */
    function updateUserTable(users) {
      const usersData = document.getElementById("usersData")
      if (!usersData) return
  
      usersData.innerHTML = ""
  
      if (users.length === 0) {
        // Afficher un message quand aucun utilisateur n'est trouvé
        usersData.innerHTML = `
          <li class="no-data-message">
            Aucun utilisateur trouvé
          </li>
        `
        return
      }
  
      users.forEach((user) => {
        const userItem = document.createElement("li")
        userItem.className = "user-item"
  
        // Déterminer le statut
        const statusClass = user.status == 1 ? "status-active" : "status-inactive-red"
        const statusText = user.status == 1 ? "Actif" : "Désactivé"
  
        // Déterminer la classe du rôle
        let roleClass = ""
        const roleName = (user.role || "").toLowerCase()
  
        if (roleName.includes("admin")) {
          roleClass = "role-admin"
        } else if (roleName.includes("responsable")) {
          roleClass = "role-responsable"
        } else if (roleName.includes("agent")) {
          roleClass = "role-agent"
        }
  
        userItem.innerHTML = `
          <div class="user-name">${user.nom_user || ""} ${user.prenom_user || ""}</div>
          <div class="user-username">${user.username || ""}</div>
          <div class="user-status">
            <span class="status-badge ${statusClass}">${statusText}</span>
          </div>
          <div class="user-structure">${user.direction || "Non défini"}</div>
          <div class="user-role">
            <span class="role-badge ${roleClass}">${user.role || "Non défini"}</span>
          </div>
          <div class="user-actions">
            <a href="ActionUser.php?id=${user.id}" class="btn-action">
              <i class='bx bx-cog'></i> Actions
            </a>
          </div>
        `
  
        usersData.appendChild(userItem)
      })
  
      // Animer les éléments de la liste
      animateListItems()
    }
  
    /**
     * Ajoute des animations aux éléments de la liste
     */
    function animateListItems() {
      const items = document.querySelectorAll(".user-item")
      items.forEach((item, index) => {
        item.style.opacity = "0"
        item.style.transform = "translateY(20px)"
  
        setTimeout(
          () => {
            item.style.transition = "all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)"
            item.style.opacity = "1"
            item.style.transform = "translateY(0)"
          },
          100 + index * 50,
        )
      })
    }
  
    /**
     * Ajoute des effets visuels aux champs de formulaire
     */
    function addFormFieldEffects() {
      // Ajouter des effets de survol et de focus
      document.querySelectorAll(".form-control").forEach((field) => {
        // Effet au survol
        field.addEventListener("mouseenter", function () {
          if (!this.classList.contains("error-input") && document.activeElement !== this) {
            this.style.borderColor = "#3b82f6" // primary-light
          }
        })
  
        field.addEventListener("mouseleave", function () {
          if (!this.classList.contains("error-input") && document.activeElement !== this) {
            this.style.borderColor = "#dee2e6" // grey-200
          }
        })
  
        // Effet au focus
        field.addEventListener("focus", function () {
          this.parentElement.classList.add("focused")
          const label = this.closest(".form-group").querySelector("label")
          if (label) {
            label.style.color = "#007bff" // primary
          }
        })
  
        field.addEventListener("blur", function () {
          this.parentElement.classList.remove("focused")
          const label = this.closest(".form-group").querySelector("label")
          if (label) {
            label.style.color = "#495057" // grey-600
          }
        })
      })
    }
  
    /**
     * Ajoute un effet de ripple aux boutons
     */
    function addRippleEffect() {
      const buttons = document.querySelectorAll(".btn, .btn-action")
  
      buttons.forEach((button) => {
        button.addEventListener("click", (e) => {
          const rect = button.getBoundingClientRect()
          const x = e.clientX - rect.left
          const y = e.clientY - rect.top
  
          const ripple = document.createElement("span")
          ripple.classList.add("ripple")
          ripple.style.left = `${x}px`
          ripple.style.top = `${y}px`
  
          button.appendChild(ripple)
  
          setTimeout(() => {
            ripple.remove()
          }, 800)
        })
      })
    }
  
    /**
     * Fonction d'animation pour afficher une modal
     */
    function showModal(modal) {
      if (!modal) return
  
      modal.style.display = "block"
      // Force le navigateur à reconnaître le changement pour l'animation
      setTimeout(() => {
        modal.classList.add("show")
      }, 10)
    }
  
    /**
     * Fonction d'animation pour cacher une modal
     */
    function hideModal(modal) {
      if (!modal) return
  
      modal.classList.remove("show")
      setTimeout(() => {
        modal.style.display = "none"
      }, 300) // Durée de l'animation
    }
  
    // Animation de secousse pour les erreurs
    document.head.insertAdjacentHTML(
      "beforeend",
      `
        <style>
          @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
          }
          
          .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            pointer-events: none;
          }
          
          @keyframes ripple {
            to {
              transform: scale(4);
              opacity: 0;
            }
          }
        </style>
      `,
    )
  })
  