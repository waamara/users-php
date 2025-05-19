document.addEventListener("DOMContentLoaded", () => {
    const editform = document.getElementById("appelOffreForm");
    const addform = document.getElementById("addAppelOffreForm");
    const modal = document.getElementById("appelOffreModal");
    const addmodal = document.getElementById("addAppelOffreModal");
    const closeModal = document.querySelector(".close-btn");
    const closeAddModal = document.querySelector(".addClose-btn");
    const addBtn = document.getElementById("addAppelOffreBtn");
    const tableBody = document.querySelector("#appelOffreTable tbody");
    const searchInput = document.getElementById("searchInput");
  
    const codeFieldadd = document.getElementById("addCode");
    const dateFieldadd = document.getElementById("addDateAO");
    const codeErroradd = document.getElementById("addCodeError");
    const dateErroradd = document.getElementById("addDateError");
    const idFieldadd = document.getElementById("aadAppelOffreId");
  
    const idField = document.getElementById("appelOffreId");
    const codeField = document.getElementById("code");
    const dateField = document.getElementById("dateAO");
    const codeError = document.getElementById("codeError");
    const dateError = document.getElementById("dateError");
  
    // Function to format date from yyyy-mm-dd to dd-mm-yyyy
    function formatDateForDisplay(dateString) {
      if (!dateString) return "";
  
      // Check if the date is already in dd-mm-yyyy format
      if (/^\d{2}-\d{2}-\d{4}$/.test(dateString)) {
        return dateString;
      }
  
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return dateString; // Return original if invalid
  
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();
  
      return `${day}-${month}-${year}`;
    }
  
    //✅ Fetch Offers Functionality
    function fetchOffers() {
      fetch("../../Backend/Appel_Offre/requetes_ajax/fetch_appels.php")
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            Swal.fire({
              icon: "error",
              title: "Error!",
              text: data.error,
            });
            return;
          }
  
          // Clear existing table rows
          tableBody.innerHTML = "";
  
          // Loop through the fetched data and add rows dynamically
          data.forEach((offer) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${offer.id}</td>
                <td>${offer.num_appel_offre}</td>
                <td>${formatDateForDisplay(offer.date_appel_offre)}</td>
                <td>
                    <button id="edit-btn" class="btn btn-edit" data-id="${offer.id}">Modifier</button>
                </td>
            `;
            tableBody.appendChild(row);
          });
          attachEditEventListeners();
        })
        .catch((error) => console.error("Fetch Error:", error));
    }
    fetchOffers();
  
    //✅ Search Functionality
    searchInput.addEventListener("input", () => {
      const searchTerm = searchInput.value.trim().toLowerCase();
      searchOffers(searchTerm);
    });
  
    function searchOffers(searchTerm) {
      fetch(`../../Backend/Appel_Offre/requetes_ajax/search_appels.php?query=${encodeURIComponent(searchTerm)}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            Swal.fire({
              icon: "error",
              title: "Erreur!",
              text: data.error,
            });
            return;
          }
  
          // Clear existing table rows
          tableBody.innerHTML = "";
  
          // Loop through search results and update the table
          data.forEach((offer) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${offer.id}</td>
                <td>${offer.num_appel_offre}</td>
                <td>${formatDateForDisplay(offer.date_appel_offre)}</td>
                <td>
                    <button class="btn btn-edit" data-id="${offer.id}">Modifier</button>
                </td>
            `;
            tableBody.appendChild(row);
          });
  
          attachEditEventListeners();
        })
        .catch((error) => console.error("Fetch Error:", error));
    }
  
    //✅ Add Functionality
    // The Adding Form Clicking
    addBtn.addEventListener("click", () => {
      addform.reset(); // Reset input fields
      clearAddModalErrors();
      idFieldadd.value = ""; // Ensure no previous ID is stored
      addmodal.style.display = "flex"; // Show modal
    });
    // The Adding Form Submission
    addform.addEventListener("submit", (e) => {
      e.preventDefault();
  
      const id = idFieldadd.value.trim();
      const code = codeFieldadd.value.trim();
      const dateAO = dateFieldadd.value.trim();
      let valid = true;
  
      // Validate Code Field
      if (!code) {
        codeErroradd.textContent = "Ce champ est obligatoire.";
        codeErroradd.style.display = "block";
        valid = false;
      }
  
      // Validate Date Field
      if (!dateAO) {
        dateErroradd.textContent = "Ce champ est obligatoire.";
        dateErroradd.style.display = "block";
        valid = false;
      } else {
        dateErroradd.textContent = ""; // Clear error message if date is entered
        dateErroradd.style.display = "none"; // Hide error message
      }
  
      if (!valid) return;
  
      const jsonData = { id, code, dateAO };
  
      fetch("../../Backend/Appel_Offre/requetes_ajax/add_appel.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(jsonData),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({ icon: "success", title: "Succès!", text: data.success });
            fetchOffers();
            addmodal.style.display = "none";
          } else {
            Swal.fire({ icon: "error", title: "Erreur!", text: data.error });
          }
        })
        .catch((error) => Swal.fire({ icon: "error", title: "Erreur!", text: "Problème lors de la mise à jour." }));
    });
  
    // ✅ Edit Functionality
    function attachEditEventListeners() {
      document.querySelectorAll(".btn-edit").forEach((button) => {
        button.addEventListener("click", (event) => {
          const offerId = event.target.closest("button").dataset.id;
          clearErrors();
          loadOfferDetails(offerId);
        });
      });
    }
    
    function loadOfferDetails(offerId) {
      fetch(`../../Backend/Appel_Offre/requetes_ajax/get_appel.php?id=${offerId}`)
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            Swal.fire({ icon: "error", title: "Erreur!", text: data.error });
            return;
          }
  
          idField.value = data.id;
          codeField.value = data.num_appel_offre;
          dateField.value = data.date_appel_offre;
  
          document.getElementById("modalTitle").textContent = "Modifier Un Appel d'Offre";
          modal.style.display = "flex";
        })
        .catch((error) => console.error("Fetch Error:", error));
    }
  
    // The Editing Form Submission
    editform.addEventListener("submit", (e) => {
      e.preventDefault();
  
      const id = idField.value.trim();
      const code = codeField.value.trim();
      const dateAO = dateField.value.trim();
      let valid = true;
  
      // Validate Code Field
      if (!code) {
        codeError.textContent = "Ce champ est obligatoire.";
        codeError.style.display = "block";
        valid = false;
      }
  
      // Validate Date Field
      if (!dateAO) {
        dateError.textContent = "Ce champ est obligatoire.";
        dateError.style.display = "block";
        valid = false;
      } else {
        dateError.textContent = ""; // Clear error message if date is entered
        dateError.style.display = "none"; // Hide error message
      }
  
      // Stop form submission if there are validation errors
      if (!valid) return;
  
      const jsonData = { id, code, dateAO };
  
      fetch("../../Backend/Appel_Offre/requetes_ajax/update_appel.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(jsonData),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({ icon: "success", title: "Succès!", text: data.success });
            fetchOffers();
            modal.style.display = "none";
          } else {
            Swal.fire({ icon: "error", title: "Erreur!", text: data.error });
          }
        })
        .catch((error) => Swal.fire({ icon: "error", title: "Erreur!", text: "Problème lors de la mise à jour." }));
    });
  
    //✅ Close Modals Functionality
    //The Editing Modal
    closeModal.addEventListener("click", () => {
      modal.style.display = "none";
    });
    window.addEventListener("click", (e) => {
      if (e.target === modal) modal.style.display = "none";
    });
  
    //The Adding Modal
    closeAddModal.addEventListener("click", () => (addmodal.style.display = "none"));
    window.addEventListener("click", (e) => {
      if (e.target === addmodal) addmodal.style.display = "none";
    });
  
    // ✅ Check Code Uniqueness Functionality
    // Edit Modal
    function checkCodeUniqueness() {
      const code = codeField.value.trim();
      const id = idField.value.trim(); // Get the current ID
  
      fetch("../../Backend/Appel_Offre/requetes_ajax/check_code.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code: code, id: id }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            codeError.textContent = data.error;
            codeError.style.display = "block";
          } else {
            codeError.textContent = "";
            codeError.style.display = "none";
          }
        })
        .catch((error) => console.error("AJAX Error:", error));
    }
    codeField.addEventListener("input", () => {
      if (codeField.value.trim().length > 0) {
        checkCodeUniqueness(); // Call function when user types
      } else {
        codeError.textContent = "";
        codeError.style.display = "none";
      }
    });
  
    // Add Modal
    function checkAddCodeUniqueness() {
      const code = codeFieldadd.value.trim();
      const id = idFieldadd.value.trim(); // Get the current ID (should be empty for new records)
  
      fetch("../../Backend/Appel_Offre/requetes_ajax/check_code.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ code: code, id: id }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.error) {
            codeErroradd.textContent = data.error;
            codeErroradd.style.display = "block";
          } else {
            codeErroradd.textContent = "";
            codeErroradd.style.display = "none";
          }
        })
        .catch((error) => console.error("AJAX Error:", error));
    }
    codeFieldadd.addEventListener("input", () => {
      if (codeFieldadd.value.trim().length > 0) {
        checkAddCodeUniqueness();
      } else {
        codeErroradd.textContent = "";
        codeErroradd.style.display = "none";
      }
    });
  
    // ✅ Clear Error Messages from modals
    // Edit Modal
    function clearErrors() {
      codeError.textContent = "";
      dateError.textContent = "";
      codeError.style.display = "none";
      dateError.style.display = "none";
    }
    // Add Modal
    function clearAddModalErrors() {
      codeErroradd.textContent = "";
      dateErroradd.textContent = "";
      codeErroradd.style.display = "none";
      dateErroradd.style.display = "none";
    }
});
