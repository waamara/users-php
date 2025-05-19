document.addEventListener("DOMContentLoaded", () => {
  // Only clear session if user has completely left the application
  // and is not just refreshing or navigating between pages
  if (
    sessionStorage.getItem("leftLiberationPages") === "true" &&
    !document.referrer.includes(window.location.hostname)
  ) {
    // Clear the session data for search
    fetch("clear-session.php?action=clear_liberation_search")
      .then((response) => response.text())
      .then((data) => {
        console.log("Session cleared")
        // Clear the sessionStorage flag
        sessionStorage.removeItem("leftLiberationPages")
        // Reload the page without parameters if we have search parameters in URL
        if (window.location.search && !window.location.search.includes("from_details=true")) {
          window.location.href = window.location.pathname
        }
      })
      .catch((error) => {
        console.error("Error clearing session:", error)
      })
  }

  // Basic form validation
  const filterForm = document.getElementById("filter-form")
  const startDateInput = document.getElementById("startDate")
  const endDateInput = document.getElementById("endDate")
  const dateError = document.getElementById("date-error")
  const resultsContainer = document.querySelector(".card:nth-child(3)")

  if (filterForm) {
    filterForm.addEventListener("submit", (e) => {
      if (dateError) {
        dateError.textContent = ""
      }

      // Check if at least one date is provided
      if ((!startDateInput || !startDateInput.value) && (!endDateInput || !endDateInput.value)) {
        e.preventDefault()
        if (dateError) {
          dateError.textContent = "Veuillez sélectionner au moins une date"
        }

        // Hide any existing results when validation fails
        if (resultsContainer) {
          resultsContainer.style.display = "none"
        }

        return false
      }

      // Check if dates are valid when both are provided
      if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
        const startDate = new Date(startDateInput.value)
        const endDate = new Date(endDateInput.value)

        if (startDate > endDate) {
          e.preventDefault()
          if (dateError) {
            dateError.textContent = "La date de début doit être antérieure à la date de fin"
          }

          // Hide any existing results when validation fails
          if (resultsContainer) {
            resultsContainer.style.display = "none"
          }

          return false
        }
      }

      return true
    })
  }

  // Reset error on input change
  if (endDateInput) {
    endDateInput.addEventListener("input", () => {
      if (dateError) {
        dateError.textContent = ""
      }
    })
  }

  if (startDateInput) {
    startDateInput.addEventListener("input", () => {
      if (dateError) {
        dateError.textContent = ""
      }
    })
  }
})
