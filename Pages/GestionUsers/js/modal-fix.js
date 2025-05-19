document.addEventListener("DOMContentLoaded", function() {
    // Fix for modal display and z-index issues
    const fixModals = () => {
      // Ensure SweetAlert2 container has the highest z-index
      const styleElement = document.createElement('style');
      styleElement.textContent = `
        .swal2-container {
          z-index: 999999 !important;
          position: fixed !important;
        }
        
        .modal {
          z-index: 99999 !important;
          position: fixed !important;
          top: 0 !important;
          left: 0 !important;
          width: 100% !important;
          height: 100% !important;
          background-color: rgba(0, 0, 0, 0.5) !important;
          backdrop-filter: blur(5px) !important;
        }
        
        .modal-content {
          position: relative !important;
          background-color: #fff !important;
          margin: 5% auto !important;
          padding: 2rem !important;
          border-radius: 10px !important;
          width: 90% !important;
          max-width: 500px !important;
          box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2) !important;
        }
        
        .close {
          position: absolute !important;
          right: 1.25rem !important;
          top: 1rem !important;
          font-size: 1.5rem !important;
          cursor: pointer !important;
        }
      `;
      document.head.appendChild(styleElement);
      
      // Override SweetAlert2 default settings to ensure proper z-index
      if (window.Swal) {
        // Store original Swal.fire method
        const originalSwalFire = window.Swal.fire;
        
        // Override Swal.fire to force z-index
        window.Swal.fire = function(...args) {
          const result = originalSwalFire.apply(this, args);
          
          // Force SweetAlert container to highest z-index
          setTimeout(() => {
            const swalContainers = document.querySelectorAll('.swal2-container');
            swalContainers.forEach(container => {
              container.style.cssText += 'z-index: 999999 !important; position: fixed !important;';
            });
          }, 0);
          
          return result;
        };
      }
    };
    
    // Fix modal show/hide functions
    const fixModalFunctions = () => {
      // Improved show modal function
      window.showModal = function(modal) {
        if (!modal) return;
        
        // Force display block
        modal.style.cssText += 'display: block !important; opacity: 1 !important;';
        modal.classList.add('show');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
        
        // Force modal content to be visible
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
          modalContent.style.cssText += 'transform: translateY(0) scale(1) !important;';
        }
      };
      
      // Improved hide modal function
      window.hideModal = function(modal) {
        if (!modal) return;
        
        // Force hide
        modal.style.cssText += 'opacity: 0 !important;';
        modal.classList.remove('show');
        
        // Wait for animation
        setTimeout(() => {
          modal.style.cssText += 'display: none !important;';
          document.body.style.overflow = '';
        }, 300);
      };
    };
    
    // Run fixes
    fixModals();
    fixModalFunctions();
  });