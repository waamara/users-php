document.addEventListener("DOMContentLoaded", () => {
    const monnaieTableBody = document.querySelector("#monnaieTable tbody")
    const addform = document.getElementById("monnaieForm")
    const addmodal = document.getElementById("monnaieModal")
    const addBtn = document.getElementById("addMonnaieBtn")
    const searchInput = document.getElementById("searchInput")
    const closeAddModal = document.querySelector(".close-btn")
  
    const codeFieldadd = document.getElementById("code")
    const labelFieldadd = document.getElementById("label")
    const symboleFieldadd = document.getElementById("symbole")
    const codeErroradd = document.getElementById("codeError")
    const labelErroradd = document.getElementById("labelError")
    const symboleErroradd = document.getElementById("symboleError")
    const idFieldadd = document.getElementById("monnaieId")
  
    let isCodeUnique = false
    let isLabelUnique = false
  
    // Function to format date from yyyy-mm-dd to dd-mm-yyyy
    function formatDateForDisplay(dateString) {
      if (!dateString) return ""
  
      // Check if the date is already in dd-mm-yyyy format
      if (/^\d{2}-\d{2}-\d{4}$/.test(dateString)) {
        return dateString
      }
  
      const date = new Date(dateString)
      if (isNaN(date.getTime())) return dateString // Return original if invalid
  
      const day = String(date.getDate()).padStart(2, "0")
      const month = String(date.getMonth() + 1).padStart(2, "0")
      const year = date.getFullYear()
  
      return `${day}-${month}-${year}`
    }
  
    //✅ Fetch Monnaie Functionality
    function fetchMonnaies() {
      fetch("../../Backend/Monnaie/requetes_ajax/fetch_monnaies.php")
        .then((response) => {
          return response.json() // Convert response to JSON
        })
        .then((data) => {
          if (data.error) {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: data.error,
            })
            return
          }
  
          // Clear existing table rows
          monnaieTableBody.innerHTML = ""
  
          // Loop through the fetched data and add rows dynamically
          data.forEach((monnaie) => {
            const row = document.createElement("tr")
            row.innerHTML = `
                          <td>${monnaie.id}</td>
                          <td>${monnaie.code}</td>
                          <td>${monnaie.label}</td>
                          <td>${monnaie.symbole}</td>
                          <td>
                              <button class="btn btn-edit" data-id="${monnaie.id}">Modifier</button>
                          </td>
                      `
            monnaieTableBody.appendChild(row)
          })
          attachEditEventListeners()
        })
        .catch((error) => console.error("Fetch Error:", error))
    }
    fetchMonnaies()
  
    //✅ Search Functionality
    searchInput.addEventListener("input", () => {
      const searchTerm = searchInput.value.trim().toLowerCase()
      searchMonnaies(searchTerm)
    })
  
    function searchMonnaies(searchTerm) {
      fetch(`../../Backend/Monnaie/requetes_ajax/search_monnaies.php?query=${encodeURIComponent(searchTerm)}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            Swal.fire({
              icon: "error",
              title: "Erreur!",
              text: data.error,
            })
            return
          }
  
          monnaieTableBody.innerHTML = ""
  
          // ✅ Loop through search results and update the table
          data.forEach((monnaie) => {
            const row = document.createElement("tr")
            row.innerHTML = `
                          <td>${monnaie.id}</td>
                          <td>${monnaie.code}</td>
                          <td>${monnaie.label}</td>
                          <td>${monnaie.symbole}</td>
                          <td>
                              <button class="btn btn-edit" data-id="${monnaie.id}">Modifier</button>
                          </td>
                      `
            monnaieTableBody.appendChild(row)
          })
  
          attachEditEventListeners()
        })
        .catch((error) => console.error("Fetch Error:", error))
    }
  
    //✅ Close Modal Functionality
    closeAddModal.addEventListener("click", () => {
      addmodal.style.display = "none"
    })
    window.addEventListener("click", (e) => {
      if (e.target === addmodal) addmodal.style.display = "none"
    })
  
    //✅ Add Functionality
    addBtn.addEventListener("click", () => {
      clearAddModalErrors()
      addform.reset()
      idFieldadd.value = ""
      document.getElementById("modalTitle").textContent = "Ajouter Monnaie"
      addmodal.style.display = "flex"
    })
  
    async function checkCodeUniqueness() {
      const code = codeFieldadd.value.trim()
      const id = idFieldadd.value.trim() // Get the current ID
      const response = await fetch("../../Backend/Monnaie/requetes_ajax/check_code_monnaies.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code, id }), // Include ID in the request
      })
      const data = await response.json()
      if (data.error) {
        codeErroradd.textContent = data.error
        codeErroradd.style.display = "block"
        isCodeUnique = false
      } else {
        codeErroradd.textContent = ""
        codeErroradd.style.display = "none"
        isCodeUnique = true
      }
    }
  
    async function checkLabelUniqueness() {
      const label = labelFieldadd.value.trim()
      const id = idFieldadd.value.trim() // Get the current ID
      const response = await fetch("../../Backend/Monnaie/requetes_ajax/check_label.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ label, id }), // Include ID in the request
      })
      const data = await response.json()
      if (data.error) {
        labelErroradd.textContent = data.error
        labelErroradd.style.display = "block"
        isLabelUnique = false
      } else {
        labelErroradd.textContent = ""
        labelErroradd.style.display = "none"
        isLabelUnique = true
      }
    }
  
    symboleFieldadd.addEventListener("input", () => {
      symboleErroradd.textContent = ""
      symboleErroradd.style.display = "none"
    })
    codeFieldadd.addEventListener("input", checkCodeUniqueness)
    labelFieldadd.addEventListener("input", checkLabelUniqueness)
  
    addform.addEventListener("submit", async (e) => {
      e.preventDefault()
      await checkCodeUniqueness()
      await checkLabelUniqueness()
  
      if (!isCodeUnique || !isLabelUnique) {
        return
      }
  
      const id = idFieldadd.value.trim()
      const code = codeFieldadd.value.trim()
      const label = labelFieldadd.value.trim()
      const symbole = symboleFieldadd.value.trim()
  
      let valid = true
  
      if (!code) {
        codeErroradd.textContent = "Ce champ est obligatoire."
        codeErroradd.style.display = "block"
        valid = false
      }
      if (!label) {
        labelErroradd.textContent = "Ce champ est obligatoire."
        labelErroradd.style.display = "block"
        valid = false
      }
      if (!symbole) {
        symboleErroradd.textContent = "Ce champ est obligatoire."
        symboleErroradd.style.display = "block"
        valid = false
      }
      if (!valid) return
  
      const jsonData = { id, code, label, symbole }
  
      // If id exists, update instead of add
      const url = id ? "../../Backend/Monnaie/requetes_ajax/update_monnaie.php" : "../../Backend/Monnaie/requetes_ajax/add_monnaie.php"
  
      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(jsonData),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({ icon: "success", title: "Succès!", text: data.success })
            fetchMonnaies()
            addmodal.style.display = "none"
          } else {
            Swal.fire({ icon: "error", title: "Erreur!", text: data.error })
          }
        })
        .catch((error) => Swal.fire({ icon: "error", title: "Erreur!", text: "Problème lors de l'opération." }))
    })
  
    //✅ Edit Functionality
    function attachEditEventListeners() {
      document.querySelectorAll(".btn-edit").forEach((button) => {
        button.addEventListener("click", (event) => {
          const monnaieId = event.target.closest("button").dataset.id
          clearAddModalErrors()
          loadMonnaieDetails(monnaieId)
        })
      })
    }
  
    function loadMonnaieDetails(monnaieId) {
      fetch(`../../Backend/Monnaie/requetes_ajax/get_monnaie.php?id=${monnaieId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            Swal.fire({ icon: "error", title: "Erreur!", text: data.error })
            return
          }
  
          idFieldadd.value = data.id
          codeFieldadd.value = data.code
          labelFieldadd.value = data.label
          symboleFieldadd.value = data.symbole
  
          document.getElementById("modalTitle").textContent = "Modifier Monnaie"
          addmodal.style.display = "flex"
        })
        .catch((error) => console.error("Fetch Error:", error))
    }
  
    //✅ Clear Errors Functionality
    function clearAddModalErrors() {
      codeErroradd.textContent = ""
      labelErroradd.textContent = ""
      symboleErroradd.textContent = ""
      codeErroradd.style.display = "none"
      labelErroradd.style.display = "none"
      symboleErroradd.style.display = "none"
    }
  })
  
  