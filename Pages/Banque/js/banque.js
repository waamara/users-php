document.addEventListener("DOMContentLoaded", () => {
  // ✅ Sélection des éléments DOM
  const addForm = document.getElementById("banqueForm")
  const editForm = document.getElementById("editBanqueForm")
  const addModal = document.getElementById("banqueModal")
  const editModal = document.getElementById("editBanqueModal")
  const closeBtns = document.querySelectorAll(".close, .addClose-btn")
  const btnAnnulerAdd = document.getElementById("btnAnnulerAdd")
  const btnAnnulerEdit = document.getElementById("btnAnnulerEdit")
  const addBtn = document.getElementById("addBanqueBtn")
  const searchInput = document.getElementById("searchInput")
  const codeField = document.getElementById("code")
  const designationField = document.getElementById("designation")
  const codeError = document.getElementById("codeError")
  const designationError = document.getElementById("designationError")
  const editCodeField = document.getElementById("editCode")
  const editDesignationField = document.getElementById("editDesignation")
  const editCodeError = document.getElementById("editCodeError")
  const editDesignationError = document.getElementById("editDesignationError")
  const banquesData = document.getElementById("banquesData")
  const noResultsDiv = document.getElementById("noResults")
  const paginationContainer = document.getElementById("paginationContainer")
  const paginationInfo = document.getElementById("paginationInfo")
  const pagination = document.getElementById("pagination")

  // Configuration de la pagination
  const itemsPerPage = 10
  let currentPage = 1
  let totalItems = 0
  let totalPages = 0
  let currentData = []
  let filteredData = []

  // ✅ Fonction pour récupérer les banques depuis le backend
  function fetchBanques(query = "") {
    fetch(`../../Backend/Banque/requetes_ajax/search.php?query=${encodeURIComponent(query)}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          showSweetAlert("error", "Erreur !", data.error)
          return
        }

        // Stocker les données pour la pagination
        currentData = data
        filteredData = data
        totalItems = data.length
        totalPages = Math.ceil(totalItems / itemsPerPage)

        // Réinitialiser la page courante si nécessaire
        if (currentPage > totalPages) {
          currentPage = totalPages > 0 ? totalPages : 1
        }

        // Afficher les données
        displayBanques()

        // Mettre à jour la pagination
        updatePagination()
      })
      .catch((error) => {
        console.error("Erreur lors du chargement des banques :", error)
        showSweetAlert("error", "Erreur !", "Une erreur est survenue lors du chargement des banques.")
      })
  }

  // ✅ Fonction pour afficher les banques avec pagination
  function displayBanques() {
    banquesData.innerHTML = ""

    if (filteredData.length === 0) {
      banquesData.innerHTML = `<li class="no-data-message">Aucune banque trouvée</li>`
      paginationContainer.style.display = "none"
      return
    }

    // Calculer l'index de début et de fin pour la page courante
    const startIndex = (currentPage - 1) * itemsPerPage
    const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length)

    // Afficher les éléments de la page courante
    for (let i = startIndex; i < endIndex; i++) {
      const banque = filteredData[i]
      const item = document.createElement("li")
      item.className = "direction-item"
      item.setAttribute("data-id", banque.id)
      item.setAttribute("data-code", banque.code)
      item.setAttribute("data-label", banque.label)

      item.innerHTML = `
                <div class="direction-item-number">${i + 1}</div>
                <div class="direction-content">
                    <div class="direction-code">${banque.code}</div>
                    <div class="direction-libelle">${banque.label}</div>
                </div>
                <div class="direction-actions">
                    <button class="btn-edit" data-id="${banque.id}" data-code="${banque.code}" data-label="${banque.label}">
                        <i class='bx bx-edit'></i> Modifier
                    </button>
                    <button class="btn-agence" data-id="${banque.id}">
                        <i class='bx bx-building'></i> Agences
                    </button>
                </div>
            `

      banquesData.appendChild(item)
    }

    // Ajouter des animations aux éléments de la liste
    animateListItems()

    // Attacher les écouteurs d'événements
    attachEditListeners()
    attachAgenceListeners()
  }

  // ✅ Fonction pour mettre à jour la pagination
  function updatePagination() {
    if (totalPages <= 1) {
      paginationContainer.style.display = "none"
      return
    }

    paginationContainer.style.display = "flex"

    // Mettre à jour les informations de pagination
    const startItem = (currentPage - 1) * itemsPerPage + 1
    const endItem = Math.min(startItem + itemsPerPage - 1, totalItems)
    paginationInfo.textContent = `Affichage de ${startItem} à ${endItem} sur ${totalItems} banques`

    // Générer les liens de pagination
    pagination.innerHTML = ""

    // Bouton précédent
    const prevLi = document.createElement("li")
    prevLi.className = currentPage === 1 ? "disabled" : ""
    prevLi.innerHTML =
      currentPage === 1
        ? `<span><i class='bx bx-chevron-left'></i></span>`
        : `<a href="#" data-page="${currentPage - 1}"><i class='bx bx-chevron-left'></i></a>`
    pagination.appendChild(prevLi)

    // Calculer les pages à afficher
    let startPage = Math.max(1, currentPage - 2)
    const endPage = Math.min(totalPages, startPage + 4)

    // Ajuster si on est proche de la fin
    if (endPage - startPage < 4) {
      startPage = Math.max(1, endPage - 4)
    }

    // Première page et ellipsis
    if (startPage > 1) {
      const firstLi = document.createElement("li")
      firstLi.innerHTML = `<a href="#" data-page="1">1</a>`
      pagination.appendChild(firstLi)

      if (startPage > 2) {
        const ellipsisLi = document.createElement("li")
        ellipsisLi.className = "disabled"
        ellipsisLi.innerHTML = `<span>...</span>`
        pagination.appendChild(ellipsisLi)
      }
    }

    // Pages numérotées
    for (let i = startPage; i <= endPage; i++) {
      const pageLi = document.createElement("li")
      pageLi.className = i === currentPage ? "active" : ""
      pageLi.innerHTML = `<a href="#" data-page="${i}">${i}</a>`
      pagination.appendChild(pageLi)
    }

    // Dernière page et ellipsis
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        const ellipsisLi = document.createElement("li")
        ellipsisLi.className = "disabled"
        ellipsisLi.innerHTML = `<span>...</span>`
        pagination.appendChild(ellipsisLi)
      }

      const lastLi = document.createElement("li")
      lastLi.innerHTML = `<a href="#" data-page="${totalPages}">${totalPages}</a>`
      pagination.appendChild(lastLi)
    }

    // Bouton suivant
    const nextLi = document.createElement("li")
    nextLi.className = currentPage === totalPages ? "disabled" : ""
    nextLi.innerHTML =
      currentPage === totalPages
        ? `<span><i class='bx bx-chevron-right'></i></span>`
        : `<a href="#" data-page="${currentPage + 1}"><i class='bx bx-chevron-right'></i></a>`
    pagination.appendChild(nextLi)

    // Attacher les écouteurs d'événements aux liens de pagination
    attachPaginationListeners()
  }

  // ✅ Attacher les écouteurs d'événements aux liens de pagination
  function attachPaginationListeners() {
    document.querySelectorAll(".pagination a").forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault()
        const page = Number.parseInt(this.getAttribute("data-page"))
        if (page && page !== currentPage) {
          currentPage = page
          displayBanques()
          updatePagination()
          // Faire défiler vers le haut de la liste
          banquesData.scrollIntoView({ behavior: "smooth" })
        }
      })
    })
  }

  // ✅ Ajouter des animations aux éléments de la liste
  function animateListItems() {
    const items = document.querySelectorAll(".direction-item")
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

  // ✅ Récupère les banques au chargement de la page
  fetchBanques()

  // Debounce function to limit how often a function is called
  function debounce(func, delay) {
    let timeout
    return function () {
      const args = arguments
      clearTimeout(timeout)
      timeout = setTimeout(() => func.apply(this, args), delay)
    }
  }

  // ✅ Gestion de la recherche en temps réel
  searchInput.addEventListener(
    "input",
    debounce(() => {
      const query = searchInput.value.trim()

      if (query === "") {
        // Si la recherche est vide, afficher toutes les banques
        filteredData = currentData
        totalItems = filteredData.length
        totalPages = Math.ceil(totalItems / itemsPerPage)
        currentPage = 1
        displayBanques()
        updatePagination()
        noResultsDiv.style.display = "none"
      } else {
        // Filtrer les banques localement
        filteredData = currentData.filter((banque) => {
          const code = banque.code.toLowerCase()
          const label = banque.label.toLowerCase()
          const searchTerm = query.toLowerCase()
          return code.includes(searchTerm) || label.includes(searchTerm)
        })

        totalItems = filteredData.length
        totalPages = Math.ceil(totalItems / itemsPerPage)
        currentPage = 1

        if (filteredData.length === 0) {
          banquesData.innerHTML = ""
          paginationContainer.style.display = "none"
          noResultsDiv.style.display = "flex"
        } else {
          noResultsDiv.style.display = "none"
          displayBanques()
          updatePagination()
        }
      }
    }, 300),
  )

  // ✅ Ouvre le modal d'ajout
  addBtn.addEventListener("click", () => {
    addForm.reset()
    clearErrors()
    showModal(addModal)
  })

  // ✅ Ferme les modals
  closeBtns.forEach((button) => {
    button.addEventListener("click", () => {
      hideModal(addModal)
      hideModal(editModal)
      addForm.reset()
      editForm.reset()
      clearErrors()
    })
  })

  // Fermer les modals avec les boutons Annuler
  if (btnAnnulerAdd) {
    btnAnnulerAdd.addEventListener("click", () => {
      hideModal(addModal)
    })
  }

  if (btnAnnulerEdit) {
    btnAnnulerEdit.addEventListener("click", () => {
      hideModal(editModal)
    })
  }

  window.addEventListener("click", (e) => {
    if (e.target === addModal) hideModal(addModal)
    if (e.target === editModal) hideModal(editModal)
  })

  // ✅ Fonction pour vérifier l'unicité
  function checkUniqueness(field, value, errorElement, currentId = null) {
    return new Promise((resolve, reject) => {
      if (!value.trim()) {
        errorElement.textContent = ""
        errorElement.style.display = "none"
        resolve(true) // Empty field is considered valid for uniqueness check
        return
      }

      // Map 'designation' to 'label' if needed
      const fieldToSend = field === "designation" ? "label" : field

      fetch("../../Backend/Banque/requetes_ajax/unique.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          field: fieldToSend,
          value: value,
          currentId: currentId,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (!data.success) {
            // Display error message under the input field
            errorElement.textContent = data.error || `Ce ${field} existe déjà.`
            errorElement.style.display = "block"
            resolve(false) // The field is not unique
          } else {
            // Clear error message
            errorElement.textContent = ""
            errorElement.style.display = "none"
            resolve(true) // The field is unique
          }
        })
        .catch((error) => {
          console.error("Erreur lors de la vérification :", error)
          errorElement.textContent = "Une erreur est survenue lors de la vérification."
          errorElement.style.display = "block"
          resolve(false) // Assume it's not unique in case of error
        })
    })
  }

  // ✅ Ajoute des écouteurs pour la validation en temps réel
  // For the add form - UPDATED with debounce for real-time validation
  codeField.addEventListener("blur", () => {
    checkUniqueness("code", codeField.value, codeError)
  })

  codeField.addEventListener(
    "input",
    debounce(() => {
      if (codeField.value.trim() !== "") {
        checkUniqueness("code", codeField.value, codeError)
      }
    }, 500),
  )

  designationField.addEventListener("blur", () => {
    checkUniqueness("designation", designationField.value, designationError)
  })

  designationField.addEventListener(
    "input",
    debounce(() => {
      if (designationField.value.trim() !== "") {
        checkUniqueness("designation", designationField.value, designationError)
      }
    }, 500),
  )

  // For the edit form
  editCodeField.addEventListener("blur", () => {
    checkUniqueness("code", editCodeField.value, editCodeError, document.getElementById("editBanqueId").value)
  })

  editCodeField.addEventListener(
    "input",
    debounce(() => {
      if (editCodeField.value.trim() !== "") {
        checkUniqueness("code", editCodeField.value, editCodeError, document.getElementById("editBanqueId").value)
      }
    }, 500),
  )

  editDesignationField.addEventListener("blur", () => {
    checkUniqueness(
      "designation",
      editDesignationField.value,
      editDesignationError,
      document.getElementById("editBanqueId").value,
    )
  })

  editDesignationField.addEventListener(
    "input",
    debounce(() => {
      if (editDesignationField.value.trim() !== "") {
        checkUniqueness(
          "designation",
          editDesignationField.value,
          editDesignationError,
          document.getElementById("editBanqueId").value,
        )
      }
    }, 500),
  )

  // ✅ Gère la soumission du formulaire d'ajout
  addForm.addEventListener("submit", async (e) => {
    e.preventDefault()
    let valid = true
    const code = codeField.value.trim()
    const designation = designationField.value.trim()

    // Validation des champs requis
    if (code === "") {
      codeError.textContent = "Ce champ est obligatoire."
      codeError.style.display = "block"
      valid = false
    }
    if (designation === "") {
      designationError.textContent = "Ce champ est obligatoire."
      designationError.style.display = "block"
      valid = false
    }

    // Vérification de l'unicité
    if (code !== "") {
      const isCodeUnique = await checkUniqueness("code", code, codeError)
      if (!isCodeUnique) valid = false
    }

    if (designation !== "") {
      const isDesignationUnique = await checkUniqueness("designation", designation, designationError)
      if (!isDesignationUnique) valid = false
    }

    // Soumet le formulaire si toutes les validations sont passées
    if (valid) {
      fetch("../../Backend/Banque/requetes_ajax/ajouterBanque.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code, designation }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Success - close modal and show success message
            hideModal(addModal)
            showSweetAlert("success", "Succès !", data.success)
            fetchBanques() // Actualise la liste des banques
          } else {
            // Check for specific error messages from the server
            if (data.error === "Le code existe déjà.") {
              // Show error under code field
              codeError.textContent = data.error
              codeError.style.display = "block"
              return // Don't show SweetAlert
            } else if (data.error === "La désignation existe déjà.") {
              // Show error under designation field
              designationError.textContent = data.error
              designationError.style.display = "block"
              return // Don't show SweetAlert
            } else if (data.error === "Le code ou la désignation existe déjà.") {
              // Show error under both fields
              codeError.textContent = "Le code ou la désignation existe déjà."
              codeError.style.display = "block"
              designationError.textContent = "Le code ou la désignation existe déjà."
              designationError.style.display = "block"
              return // Don't show SweetAlert
            }

            // For other errors, show SweetAlert
            showSweetAlert("error", "Erreur !", data?.error || "Une erreur inattendue est survenue.")
          }
        })
        .catch((error) => {
          console.error("Erreur AJAX :", error)
          showSweetAlert("error", "Erreur !", "Une erreur est survenue lors de l'envoi des données.")
        })
    }
  })

  // ✅ Attache des écouteurs aux boutons "Modifier"
  function attachEditListeners() {
    document.querySelectorAll(".btn-edit").forEach((button) => {
      button.addEventListener("click", (e) => {
        e.preventDefault()
        e.stopPropagation() // Empêcher la propagation au parent

        const banqueId = e.currentTarget.dataset.id
        const code = e.currentTarget.dataset.code
        const label = e.currentTarget.dataset.label

        document.getElementById("editBanqueId").value = banqueId
        editCodeField.value = code
        editDesignationField.value = label

        clearErrors() // Clear any previous error messages
        showModal(editModal)
      })
    })

    // Rendre les éléments de liste cliquables pour l'édition
    document.querySelectorAll(".direction-item").forEach((item) => {
      item.addEventListener("click", function (e) {
        // Ne pas déclencher si on a cliqué sur un bouton
        if (e.target.closest(".btn-edit") || e.target.closest(".btn-agence")) {
          return
        }

        const banqueId = this.getAttribute("data-id")
        const code = this.getAttribute("data-code")
        const label = this.getAttribute("data-label")

        document.getElementById("editBanqueId").value = banqueId
        editCodeField.value = code
        editDesignationField.value = label

        clearErrors()
        showModal(editModal)
      })
    })
  }

  // ✅ Gère la soumission du formulaire de modification
  editForm.addEventListener("submit", async (e) => {
    e.preventDefault()
    let valid = true
    const banqueId = document.getElementById("editBanqueId").value
    const newCode = editCodeField.value.trim()
    const newDesignation = editDesignationField.value.trim()

    // Trouver la banque originale
    const originalBanque = currentData.find((b) => b.id === banqueId)
    const originalCode = originalBanque ? originalBanque.code : ""
    const originalLabel = originalBanque ? originalBanque.label : ""

    // Vérifie si aucune modification n'a été effectuée
    if (newCode === originalCode && newDesignation === originalLabel) {
      hideModal(editModal) // Ferme le modal
      showSweetAlert("info", "Aucun changement !", "Aucune modification n'a été effectuée.")
      return
    }

    // Validation des champs requis
    if (newCode === "") {
      editCodeError.textContent = "Ce champ est obligatoire."
      editCodeError.style.display = "block"
      valid = false
    }
    if (newDesignation === "") {
      editDesignationError.textContent = "Ce champ est obligatoire."
      editDesignationError.style.display = "block"
      valid = false
    }

    // Vérification de l'unicité uniquement si les valeurs ont changé
    if (newCode !== "" && newCode !== originalCode) {
      const isCodeUnique = await checkUniqueness("code", newCode, editCodeError, banqueId)
      if (!isCodeUnique) valid = false
    }

    if (newDesignation !== "" && newDesignation !== originalLabel) {
      const isDesignationUnique = await checkUniqueness("designation", newDesignation, editDesignationError, banqueId)
      if (!isDesignationUnique) valid = false
    }

    // Soumet le formulaire si toutes les validations sont passées
    if (valid) {
      fetch("../../Backend/Banque/requetes_ajax/modifier.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: banqueId, code: newCode, designation: newDesignation }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Success - close modal and show success message
            hideModal(editModal)
            showSweetAlert("success", "Succès !", data.success)
            fetchBanques() // Actualise la liste des banques
          } else {
            // Check for specific error messages from the server
            if (data.error === "Le code existe déjà.") {
              // Show error under code field
              editCodeError.textContent = data.error
              editCodeError.style.display = "block"
              return // Don't show SweetAlert
            } else if (data.error === "La désignation existe déjà.") {
              // Show error under designation field
              editDesignationError.textContent = data.error
              editDesignationError.style.display = "block"
              return // Don't show SweetAlert
            } else if (data.error === "Le code ou la désignation existe déjà.") {
              // Show error under both fields
              editCodeError.textContent = "Le code ou la désignation existe déjà."
              editCodeError.style.display = "block"
              editDesignationError.textContent = "Le code ou la désignation existe déjà."
              editDesignationError.style.display = "block"
              return // Don't show SweetAlert
            }

            // For other errors, show SweetAlert
            showSweetAlert("error", "Erreur !", data?.error || "Une erreur inattendue est survenue.")
          }
        })
        .catch((error) => {
          console.error("Erreur AJAX :", error)
          showSweetAlert("error", "Erreur !", "Une erreur est survenue lors de la modification.")
        })
    }
  })

  // ✅ Attache des écouteurs aux boutons "Agences"
  function attachAgenceListeners() {
    document.querySelectorAll(".btn-agence").forEach((button) => {
      button.addEventListener("click", (e) => {
        e.preventDefault()
        e.stopPropagation() // Empêcher la propagation au parent

        const banqueId = e.currentTarget.dataset.id

        // Stocke l'ID de la banque dans sessionStorage
        sessionStorage.setItem("selectedBanqueId", banqueId)

        // Redirige vers la page des agences
        window.location.href = "../Agence/agence.php"
      })
    })
  }

  // ✅ Efface les messages d'erreur
  function clearErrors() {
    codeError.textContent = ""
    designationError.textContent = ""
    codeError.style.display = "none"
    designationError.style.display = "none"
    editCodeError.textContent = ""
    editDesignationError.textContent = ""
    editCodeError.style.display = "none"
    editDesignationError.style.display = "none"
  }

  // ✅ Fonction d'animation pour afficher une modal
  function showModal(modal) {
    if (!modal) return

    modal.style.display = "flex"
    // Force le navigateur à reconnaître le changement pour l'animation
    setTimeout(() => {
      modal.classList.add("show")
    }, 10)
  }

  // ✅ Fonction d'animation pour cacher une modal
  function hideModal(modal) {
    if (!modal) return

    modal.classList.remove("show")
    setTimeout(() => {
      modal.style.display = "none"
    }, 300) // Durée de l'animation
  }

  // ✅ Affiche un message avec SweetAlert
  function showSweetAlert(icon, title, text) {
    Swal.fire({
      icon: icon,
      title: title,
      text: text,
      confirmButtonColor: "#2563eb",
      confirmButtonText: "OK",
      allowOutsideClick: true,
      allowEscapeKey: true,
      allowEnterKey: true,
      showConfirmButton: true,
      timer: 5000, // Affiche le message pendant 5 secondes
      timerProgressBar: true,
      showClass: {
        popup: "animate__animated animate__fadeInDown",
      },
      hideClass: {
        popup: "animate__animated animate__fadeOutUp",
      },
    })
  }

  // ✅ Ajouter un effet de ripple aux boutons
  function addRippleEffect() {
    const buttons = document.querySelectorAll(".btn, .btn-edit, .btn-agence")

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
        }, 800) // Durée de l'animation
      })
    })
  }

  // Initialiser l'effet de ripple
  addRippleEffect()
})
