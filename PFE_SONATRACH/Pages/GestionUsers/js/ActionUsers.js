document.addEventListener("DOMContentLoaded", function () {
    console.log("JS chargé");
    
    // Get DOM elements
    const userId = window.userId || null;
    const userData = window.userData || {};
    const toastContainer = document.getElementById("toastContainer");
    const editUserBtn = document.getElementById("editUserBtn");
    const userFormModal = document.getElementById("userFormModal");
    const closeFormBtn = document.getElementById("closeFormBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const userForm = document.getElementById("userForm");
    
    console.log("User ID:", userId);
    console.log("Edit button exists:", !!editUserBtn);
    console.log("Modal exists:", !!userFormModal);
    
    // --- Modal modification ---
    if (editUserBtn && userFormModal) {
        editUserBtn.addEventListener("click", function () {
            console.log("Edit button clicked");
            
            // Populate form with user data
            const nomCompletInput = document.getElementById("nomComplet");
            const userNameInput = document.getElementById("userName");
            const compteInput = document.getElementById("compte");
            const structureInput = document.getElementById("structure");
            
            if (nomCompletInput) {
                nomCompletInput.value = (userData.nom_user || "") + " " + (userData.prenom_user || "");
            }
            
            if (userNameInput) {
                userNameInput.value = userData.username || "";
            }
            
            if (compteInput) {
                compteInput.value = userData.status == 1 ? "actif" : "desactive";
            }
            
            if (structureInput) {
                structureInput.value = userData.structure || "";
            }
            
            // Show the modal
            userFormModal.classList.add("show");
        });
    }
    
    // Close modal with close button
    if (closeFormBtn && userFormModal) {
        closeFormBtn.addEventListener("click", function () {
            userFormModal.classList.remove("show");
        });
    }
    
    // Close modal with cancel button
    if (cancelBtn && userFormModal) {
        cancelBtn.addEventListener("click", function () {
            userFormModal.classList.remove("show");
        });
    }
    
    // Close modal when clicking outside
    if (userFormModal) {
        userFormModal.addEventListener("click", function (e) {
            if (e.target === userFormModal) {
                userFormModal.classList.remove("show");
            }
        });
    }
    
    // Close modal with Escape key
    window.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && userFormModal) {
            userFormModal.classList.remove("show");
        }
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        
        // Get form fields
        const nomComplet = document.getElementById("nomComplet").value.trim();
        const userName = document.getElementById("userName").value.trim();
        const compte = document.getElementById("compte").value;
        const structure = document.getElementById("structure").value;
        
        // Clear previous validation messages
        document.querySelectorAll(".validation-message").forEach(el => {
            el.textContent = "";
            el.style.display = "none";
        });
        
        document.querySelectorAll(".form-control").forEach(el => {
            el.classList.remove("is-invalid");
        });
        
        // Validate Nom Complet
        if (!nomComplet) {
            showValidationError("nomComplet", "Le nom complet est requis");
            isValid = false;
        }
        
        // Validate Username
        if (!userName) {
            showValidationError("userName", "Le nom d'utilisateur est requis");
            isValid = false;
        }
        
        // Validate Compte
        if (!compte || !["actif", "desactive"].includes(compte)) {
            showValidationError("compte", "Veuillez sélectionner un état de compte valide");
            isValid = false;
        }
        
        // Validate Structure
        if (!structure) {
            showValidationError("structure", "Veuillez sélectionner une structure");
            isValid = false;
        }
        
        return isValid;
    }
    
    function showValidationError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const validationMessage = field.parentElement.querySelector(".validation-message");
        
        field.classList.add("is-invalid");
        
        if (validationMessage) {
            validationMessage.textContent = message;
            validationMessage.style.display = "block";
            validationMessage.style.color = "red";
            validationMessage.style.fontSize = "0.875rem";
            validationMessage.style.marginTop = "0.25rem";
        }
    }
    
    // Form submission
    if (userForm) {
        userForm.addEventListener("submit", function (e) {
            e.preventDefault();
            console.log("Form submitted");
            
            if (!userId) {
                showToast("ID utilisateur manquant", "error");
                return;
            }
            
            // Validate form
            if (!validateForm()) {
                return;
            }
            
            const nomComplet = document.getElementById("nomComplet").value.trim();
            const userName = document.getElementById("userName").value.trim();
            const compte = document.getElementById("compte").value;
            const structure = parseInt(document.getElementById("structure").value) || 0;
            
            console.log("Form data:", { id: userId, nomComplet, userName, compte, structure });
            
            // Show loading state
            const submitBtn = userForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = "Traitement en cours...";
            
            fetch("../../Backend/GestionUsers/requetes_ajax/updateUser.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: userId, nomComplet, userName, compte, structure }),
            })
                .then(response => {
                    console.log("Response status:", response.status);
                    return response.json();
                })
                .then(data => {
                    console.log("Response data:", data);
                    
                    if (data.success) {
                        const [nom, prenom] = nomComplet.split(" ", 2);
                        
                        // Update UI elements
                        const fullNameEl = document.getElementById("fullName");
                        if (fullNameEl) fullNameEl.textContent = nomComplet;
                        
                        const usernameEl = document.getElementById("username");
                        if (usernameEl) usernameEl.textContent = userName;
                        
                        // Update structure display
                        const structureEl = document.getElementById("structureDisplay");
                        if (structureEl) {
                            const structureSelect = document.getElementById("structure");
                            if (structureSelect && structureSelect.selectedIndex >= 0) {
                                const selectedOption = structureSelect.options[structureSelect.selectedIndex];
                                if (selectedOption) {
                                    structureEl.textContent = selectedOption.text;
                                }
                            }
                        }
                        
                        // Update status badges
                        const statusText = compte === "actif" ? "Actif" : "Désactivé";
                        const statusIconClass = compte === "actif" ? "bx-check-circle" : "bx-block";
                        const statusBadgeClass = compte === "actif" ? "active" : "inactive";
                        
                        const accountStatus = document.getElementById("accountStatus");
                        const accountStatusBadge = document.getElementById("accountStatusBadge");
                        
                        if (accountStatus) {
                            accountStatus.classList.remove("active", "inactive");
                            accountStatus.classList.add(statusBadgeClass);
                            accountStatus.innerHTML = `<i class='bx ${statusIconClass}'></i> ${statusText}`;
                        }
                        
                        if (accountStatusBadge) {
                            accountStatusBadge.classList.remove("active", "inactive");
                            accountStatusBadge.classList.add(statusBadgeClass);
                            accountStatusBadge.innerHTML = `<i class='bx ${statusIconClass}'></i> ${statusText}`;
                        }
                        
                        // Update toggle button
                        const toggleStatusBtn = document.getElementById("toggleStatusBtn");
                        if (toggleStatusBtn) {
                            if (compte === "actif") {
                                toggleStatusBtn.classList.remove("btn-success");
                                toggleStatusBtn.classList.add("btn-danger");
                                toggleStatusBtn.innerHTML = '<i class="bx bx-power-off"></i> Désactiver le compte';
                            } else {
                                toggleStatusBtn.classList.remove("btn-danger");
                                toggleStatusBtn.classList.add("btn-success");
                                toggleStatusBtn.innerHTML = '<i class="bx bx-power-off"></i> Activer le compte';
                            }
                        }
                        
                        // Update global data
                        window.userData.nom_user = nom;
                        window.userData.prenom_user = prenom || '';
                        window.userData.username = userName;
                        window.userData.status = compte === "actif" ? 1 : 0;
                        window.userData.structure = structure;
                        
                        showToast("Utilisateur modifié avec succès", "success");
                        userFormModal.classList.remove("show");
                        
                        // Add timeline entry if function exists
                        if (typeof addTimelineEntry === 'function') {
                            addTimelineEntry("Utilisateur modifié", "Les informations de l'utilisateur ont été mises à jour");
                        }
                    } else {
                        showToast(data.message || "Erreur lors de la mise à jour", "error");
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    showToast("Erreur réseau lors de la mise à jour", "error");
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
        });
    }
    
    // --- Toast ---
    function showToast(message, type = "success") {
        if (!toastContainer) return;
        
        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        let icon = "bx-check-circle";
        if (type === "error") icon = "bx-error";
        if (type === "warning") icon = "bx-error-circle";
        toast.innerHTML = `<i class='bx ${icon}'></i><span>${message}</span>`;
        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = "fadeOut 0.5s forwards";
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
    
    // --- Timeline ---
    function addTimelineEntry(title, description) {
        const timeline = document.querySelector(".timeline");
        if (!timeline) return;
        
        const now = new Date();
        const formattedDate = `${now.getDate().toString().padStart(2, "0")}/${
            (now.getMonth() + 1).toString().padStart(2, "0")
        }/${now.getFullYear()} ${now.getHours().toString().padStart(2, "0")}:${now
            .getMinutes()
            .toString()
            .padStart(2, "0")}`;
        
        const timelineItem = document.createElement("div");
        timelineItem.className = "timeline-item";
        timelineItem.style.opacity = "0";
        timelineItem.style.transform = "translateY(10px)";
        timelineItem.innerHTML = `
            <div class="timeline-date">${formattedDate}</div>
            <div class="timeline-content">
                <div class="timeline-title">${title}</div>
                <div class="timeline-description"><p>${description}</p></div>
            </div>
        `;
        timeline.insertBefore(timelineItem, timeline.firstChild);
        setTimeout(() => {
            timelineItem.style.transition = "opacity 0.3s, transform 0.3s";
            timelineItem.style.opacity = "1";
            timelineItem.style.transform = "translateY(0)";
        }, 10);
    }
    
    // --- Activation / Désactivation ---
    const toggleStatusBtn = document.getElementById("toggleStatusBtn");
    const accountStatus = document.getElementById("accountStatus");
    const accountStatusBadge = document.getElementById("accountStatusBadge");
    
    if (toggleStatusBtn && userId) {
        toggleStatusBtn.addEventListener("click", function () {
            // Check if accountStatus exists before using it
            const isActive = accountStatus ? accountStatus.classList.contains("active") : false;
            const newStatus = isActive ? 0 : 1;
            
            fetch("../../Backend/GestionUsers/requetes_ajax/toggle_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: userId, status: newStatus }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        if (newStatus === 0) {
                            if (accountStatus) {
                                accountStatus.classList.remove("active");
                                accountStatus.classList.add("inactive");
                                accountStatus.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';
                            }
                            
                            if (accountStatusBadge) {
                                accountStatusBadge.classList.remove("active");
                                accountStatusBadge.classList.add("inactive");
                                accountStatusBadge.innerHTML = '<i class="bx bx-x-circle"></i> Inactif';
                            }
                            
                            toggleStatusBtn.classList.remove("btn-danger");
                            toggleStatusBtn.classList.add("btn-success");
                            toggleStatusBtn.innerHTML = '<i class="bx bx-power-off"></i> Activer le compte';
                            
                            showToast("Compte désactivé avec succès", "success");
                            addTimelineEntry("Compte désactivé", "Le compte a été désactivé par un administrateur");
                        } else {
                            if (accountStatus) {
                                accountStatus.classList.remove("inactive");
                                accountStatus.classList.add("active");
                                accountStatus.innerHTML = '<i class="bx bx-check-circle"></i> Actif';
                            }
                            
                            if (accountStatusBadge) {
                                accountStatusBadge.classList.remove("inactive");
                                accountStatusBadge.classList.add("active");
                                accountStatusBadge.innerHTML = '<i class="bx bx-check-circle"></i> Actif';
                            }
                            
                            toggleStatusBtn.classList.remove("btn-success");
                            toggleStatusBtn.classList.add("btn-danger");
                            toggleStatusBtn.innerHTML = '<i class="bx bx-power-off"></i> Désactiver le compte';
                            
                            showToast("Compte activé avec succès", "success");
                            addTimelineEntry("Compte activé", "Le compte a été activé par un administrateur");
                        }
                        
                        // Update global userData
                        if (window.userData) {
                            window.userData.status = newStatus;
                        }
                    } else {
                        showToast("Erreur lors de la mise à jour du statut", "error");
                    }
                })
                .catch(error => {
                    console.error("Status update error:", error);
                    showToast("Erreur réseau", "error");
                });
        });
    }
    
    // --- Reset Password ---
    const resetPasswordBtn = document.getElementById("resetPasswordBtn");
    if (resetPasswordBtn && userId) {
        resetPasswordBtn.addEventListener("click", function() {
            if (confirm("Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?")) {
                fetch("../../Backend/GestionUsers/requetes_ajax/reset_password.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: userId }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast("Mot de passe réinitialisé avec succès", "success");
                            
                            // Show temporary password if provided
                            if (data.tempPassword) {
                                alert("Mot de passe temporaire : " + data.tempPassword);
                            }
                            
                            addTimelineEntry("Mot de passe réinitialisé", "Le mot de passe a été réinitialisé par un administrateur");
                        } else {
                            showToast(data.message || "Erreur lors de la réinitialisation du mot de passe", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Password reset error:", error);
                        showToast("Erreur réseau", "error");
                    });
            }
        });
    }
});