document.addEventListener("DOMContentLoaded", () => {
  // ✅ Sélection des éléments DOM
  const addForm = document.getElementById("banqueForm")
  const editForm = document.getElementById("editBanqueForm")
  const addModal = document.getElementById("banqueModal")
  const editModal = document.getElementById("editBanqueModal")
  const closeModal = document.querySelectorAll(".close-btn")
  const addBtn = document.getElementById("addBanqueBtn")
  const searchInput = document.getElementById("searchInput") // Champ de recherche
  const codeField = document.getElementById("code")
  const designationField = document.getElementById("designation")
  const codeError = document.getElementById("codeError")
  const designationError = document.getElementById("designationError")
  const editCodeField = document.getElementById("editCode")
  const editDesignationField = document.getElementById("editDesignation")
  const editCodeError = document.getElementById("editCodeError")
  const editDesignationError = document.getElementById("editDesignationError")
  const tableBody = document.querySelector("#banquesTable tbody")

  // ✅ Fonction pour récupérer les banques depuis le backend
  function fetchBanques(query = "") {
    fetch(`../../Backend/Banque/requetes_ajax/search.php?query=${encodeURIComponent(query)}`, {
      method: "GET",
      headers: { "Content-Type": "application/json" },
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          Swal.fire({
            icon: "error",
            title: "Erreur !",
            text: data.error,
          })
          return
        }
        tableBody.innerHTML = "" // Clear the table body
        if (data.length === 0) {
          tableBody.innerHTML = `<tr><td colspan="4">Aucune donnée disponible</td></tr>`
          return
        }
        // Populate the table with search results
        data.forEach((banque) => {
          const row = document.createElement("tr")
          row.innerHTML = `
                    <td>${banque.id}</td>
                    <td>${banque.code}</td>
                    <td>${banque.label}</td>
                    <td>
                        <button class="btn btn-edit" data-id="${banque.id}" data-code="${banque.code}" data-label="${banque.label}">Modifier</button>
                        <button class="btn btn-delete" data-id="${banque.id}">Supprimer</button>
                        <button class="btn btn-agence" data-id="${banque.id}">Agences</button>
                    </td>
                `
          tableBody.appendChild(row)
        })
        attachEditListeners() // Reattach event listeners for "Modifier" buttons
        attachDeleteListeners() // Reattach event listeners for "Supprimer" buttons
        attachAgenceListeners()
      })
      .catch((error) => console.error("Erreur lors du chargement des banques :", error))
  }

  // ✅ Récupère les banques au chargement de la page
  fetchBanques()

  // ✅ Gestion de la recherche en temps réel
  searchInput.addEventListener("input", () => {
    const query = searchInput.value.trim() // Récupère la valeur du champ de recherche
    fetchBanques(query) // Recharge la liste des banques avec la requête de recherche
  })

  // ✅ Ouvre le modal d'ajout
  addBtn.addEventListener("click", () => {
    addForm.reset()
    clearErrors()
    addModal.style.display = "flex"
  })

  // ✅ Ferme les modals
  closeModal.forEach((button) => {
    button.addEventListener("click", () => {
      addModal.style.display = "none"
      editModal.style.display = "none"
      addForm.reset()
      editForm.reset()
      clearErrors()
    })
  })

  window.addEventListener("click", (e) => {
    if (e.target === addModal) addModal.style.display = "none"
    if (e.target === editModal) editModal.style.display = "none"
  })

  // ✅ Fonction pour vérifier l'unicité - UPDATED to match uniqueAgence.php pattern
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

    // Vérification de l'unicité - IMPORTANT: No SweetAlert here, just update the error messages
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
            addModal.style.display = "none"
            Swal.fire({
              icon: "success",
              title: "Succès !",
              text: data.success,
            }).then(() => {
              fetchBanques() // Actualise la liste des banques
            })
          } else {
            // CRITICAL FIX: Check for specific error messages from the server
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
            Swal.fire({
              icon: "error",
              title: "Erreur !",
              text: data?.error || "Une erreur inattendue est survenue.",
            })
          }
        })
        .catch((error) => {
          console.error("Erreur AJAX :", error)
          Swal.fire({
            icon: "error",
            title: "Erreur !",
            text: "Une erreur est survenue lors de l'envoi des données.",
          })
        })
    }
  })

  // ✅ Attache des écouteurs aux boutons "Modifier"
  function attachEditListeners() {
    document.querySelectorAll(".btn-edit").forEach((button) => {
      button.addEventListener("click", (e) => {
        const banqueId = e.currentTarget.dataset.id
        const code = e.currentTarget.dataset.code
        const label = e.currentTarget.dataset.label
        document.getElementById("editBanqueId").value = banqueId
        editCodeField.value = code
        editDesignationField.value = label
        clearErrors() // Clear any previous error messages
        editModal.style.display = "flex"
      })
    })
  }

  // ✅ Gère la soumission du formulaire de modification
  editForm.addEventListener("submit", async (e) => {
    e.preventDefault()
    let valid = true
    const banqueId = document.getElementById("editBanqueId").value
    const originalCode = document.querySelector(`button[data-id="${banqueId}"]`).dataset.code // Code initial
    const originalLabel = document.querySelector(`button[data-id="${banqueId}"]`).dataset.label // Label initial
    const newCode = editCodeField.value.trim()
    const newDesignation = editDesignationField.value.trim()

    // Vérifie si aucune modification n'a été effectuée
    if (newCode === originalCode && newDesignation === originalLabel) {
      editModal.style.display = "none" // Ferme le modal
      Swal.fire({
        icon: "info",
        title: "Aucun changement !",
        text: "Aucune modification n'a été effectuée.",
      })
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
    // IMPORTANT: No SweetAlert here, just update the error messages
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
            editModal.style.display = "none"
            Swal.fire({
              icon: "success",
              title: "Succès !",
              text: data.success,
            }).then(() => {
              fetchBanques() // Actualise la liste des banques
            })
          } else {
            // CRITICAL FIX: Check for specific error messages from the server
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
            Swal.fire({
              icon: "error",
              title: "Erreur !",
              text: data?.error || "Une erreur inattendue est survenue.",
            })
          }
        })
        .catch((error) => {
          console.error("Erreur AJAX :", error)
          Swal.fire({
            icon: "error",
            title: "Erreur !",
            text: "Une erreur est survenue lors de la modification.",
          })
        })
    }
  })

  // ✅ Attache des écouteurs aux boutons "Supprimer"
  function attachDeleteListeners() {
    document.querySelectorAll(".btn-delete").forEach((button) => {
      button.addEventListener("click", async (e) => {
        const banqueId = e.currentTarget.dataset.id

        // Confirmation avec SweetAlert2
        const result = await Swal.fire({
          title: "Êtes-vous sûr ?",
          text: "Cette action est irréversible !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Oui, supprimer !",
          cancelButtonText: "Annuler",
        })

        if (result.isConfirmed) {
          // Envoi de la requête de suppression
          fetch("../../Backend/Banque/requetes_ajax/supprimer.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: banqueId }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                Swal.fire({
                  icon: "success",
                  title: "Supprimé !",
                  text: data.success,
                })
                fetchBanques() // Actualise la liste des banques
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Erreur !",
                  text: data?.error || "Une erreur inattendue est survenue.",
                })
              }
            })
            .catch((error) => {
              console.error("Erreur AJAX :", error)
              Swal.fire({
                icon: "error",
                title: "Erreur !",
                text: "Une erreur est survenue lors de la suppression.",
              })
            })
        }
      })
    })
  }

  // ✅ Attache des écouteurs aux boutons "Agences"
  function attachAgenceListeners() {
    document.querySelectorAll(".btn-agence").forEach((button) => {
      button.addEventListener("click", (e) => {
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

  // Debounce function to limit how often a function is called
  function debounce(func, delay) {
    let timeout
    return function () {
      const args = arguments
      clearTimeout(timeout)
      timeout = setTimeout(() => func.apply(this, args), delay)
    }
  }
})
