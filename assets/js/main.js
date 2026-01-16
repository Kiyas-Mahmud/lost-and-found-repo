/**
 * University Lost & Found Management Platform
 * Main JavaScript File
 */

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", function () {
  initNavbarToggle();
  initFlashMessage();
  initDropdowns();
  initFileUpload();
  initFormValidation();
});

/**
 * Navbar Mobile Toggle
 */
function initNavbarToggle() {
  const navbarToggle = document.getElementById("navbarToggle");
  const navbarMenu = document.querySelector(".navbar-menu");

  if (navbarToggle && navbarMenu) {
    navbarToggle.addEventListener("click", function () {
      navbarMenu.classList.toggle("active");
    });
  }
}

/**
 * Flash Message Auto-hide
 */
function initFlashMessage() {
  const flashMessage = document.getElementById("flashMessage");

  if (flashMessage) {
    // Auto hide after 5 seconds
    setTimeout(function () {
      closeFlash();
    }, 5000);
  }
}

/**
 * Close Flash Message
 */
function closeFlash() {
  const flashMessage = document.getElementById("flashMessage");

  if (flashMessage) {
    flashMessage.style.animation = "slideOut 0.3s ease";

    setTimeout(function () {
      flashMessage.remove();
    }, 300);
  }
}

/**
 * Initialize Dropdowns
 */
function initDropdowns() {
  const dropdowns = document.querySelectorAll(".dropdown");

  dropdowns.forEach(function (dropdown) {
    const toggle = dropdown.querySelector(".dropdown-toggle");
    const menu = dropdown.querySelector(".dropdown-menu");

    if (toggle && menu) {
      // Mobile: toggle on click
      if (window.innerWidth <= 768) {
        toggle.addEventListener("click", function (e) {
          e.preventDefault();
          menu.style.display =
            menu.style.display === "block" ? "none" : "block";
        });
      }
    }
  });
}

/**
 * File Upload Preview
 */
function initFileUpload() {
  const fileInputs = document.querySelectorAll('input[type="file"]');

  fileInputs.forEach(function (input) {
    input.addEventListener("change", function (e) {
      const file = e.target.files[0];
      const previewId = input.getAttribute("data-preview");

      if (file && previewId) {
        const preview = document.getElementById(previewId);

        if (preview && file.type.startsWith("image/")) {
          const reader = new FileReader();

          reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = "block";
          };

          reader.readAsDataURL(file);
        }
      }
    });
  });
}

/**
 * Form Validation
 */
function initFormValidation() {
  const forms = document.querySelectorAll('form[data-validate="true"]');

  forms.forEach(function (form) {
    form.addEventListener("submit", function (e) {
      if (!validateForm(form)) {
        e.preventDefault();
      }
    });
  });
}

/**
 * Validate Form
 */
function validateForm(form) {
  let isValid = true;
  const requiredFields = form.querySelectorAll("[required]");

  requiredFields.forEach(function (field) {
    if (!field.value.trim()) {
      showFieldError(field, "This field is required");
      isValid = false;
    } else {
      clearFieldError(field);
    }
  });

  return isValid;
}

/**
 * Show Field Error
 */
function showFieldError(field, message) {
  clearFieldError(field);

  field.classList.add("error");

  const errorDiv = document.createElement("div");
  errorDiv.className = "form-error";
  errorDiv.textContent = message;

  field.parentNode.appendChild(errorDiv);
}

/**
 * Clear Field Error
 */
function clearFieldError(field) {
  field.classList.remove("error");

  const existingError = field.parentNode.querySelector(".form-error");
  if (existingError) {
    existingError.remove();
  }
}

/**
 * Confirm Delete Action
 */
function confirmDelete(message) {
  return confirm(message || "Are you sure you want to delete this item?");
}

/**
 * Format Date
 */
function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: "numeric", month: "short", day: "numeric" };
  return date.toLocaleDateString("en-US", options);
}

/**
 * Format DateTime
 */
function formatDateTime(dateString) {
  const date = new Date(dateString);
  const options = {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  };
  return date.toLocaleDateString("en-US", options);
}

/**
 * API Helper - Fetch wrapper with error handling
 */
async function apiRequest(url, options = {}) {
  try {
    const defaultOptions = {
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      credentials: "same-origin",
    };

    const response = await fetch(url, { ...defaultOptions, ...options });
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || "Request failed");
    }

    return data;
  } catch (error) {
    console.error("API Error:", error);
    throw error;
  }
}

/**
 * API GET request
 */
async function apiGet(url) {
  return apiRequest(url, { method: "GET" });
}

/**
 * API POST request
 */
async function apiPost(url, data) {
  return apiRequest(url, {
    method: "POST",
    body: JSON.stringify(data),
  });
}

/**
 * API PUT request
 */
async function apiPut(url, data) {
  return apiRequest(url, {
    method: "PUT",
    body: JSON.stringify(data),
  });
}

/**
 * API DELETE request
 */
async function apiDelete(url) {
  return apiRequest(url, { method: "DELETE" });
}

/**
 * Show Toast Notification
 */
function showToast(message, type = "success") {
  // Remove existing toast
  const existingToast = document.getElementById("toast");
  if (existingToast) {
    existingToast.remove();
  }

  // Create toast
  const toast = document.createElement("div");
  toast.id = "toast";
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <i class="fas fa-${
      type === "success" ? "check-circle" : "exclamation-circle"
    }"></i>
    <span>${message}</span>
  `;

  document.body.appendChild(toast);

  // Show toast
  setTimeout(() => toast.classList.add("show"), 100);

  // Auto hide after 3 seconds
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

/**
 * Show Loading Spinner
 */
function showLoading(element) {
  if (element) {
    const spinner = document.createElement("i");
    spinner.className = "fas fa-spinner fa-spin";
    spinner.id = "loading-spinner";
    element.appendChild(spinner);
    element.disabled = true;
  }
}

/**
 * Hide Loading Spinner
 */
function hideLoading(element) {
  if (element) {
    const spinner = document.getElementById("loading-spinner");
    if (spinner) {
      spinner.remove();
    }
    element.disabled = false;
  }
}

/**
 * Debounce Function
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Search Filter (for browse page)
 */
function initSearchFilter() {
  const searchInput = document.getElementById("searchInput");

  if (searchInput) {
    searchInput.addEventListener(
      "input",
      debounce(function (e) {
        const searchTerm = e.target.value.toLowerCase();
        const items = document.querySelectorAll(".item-card");

        items.forEach(function (item) {
          const title = item
            .querySelector(".item-title")
            .textContent.toLowerCase();
          const description =
            item
              .querySelector(".item-description")
              ?.textContent.toLowerCase() || "";

          if (title.includes(searchTerm) || description.includes(searchTerm)) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        });
      }, 300)
    );
  }
}

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
  navigator.clipboard
    .writeText(text)
    .then(function () {
      alert("Copied to clipboard!");
    })
    .catch(function (err) {
      console.error("Failed to copy:", err);
    });
}

/**
 * Scroll to Top
 */
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}

// Add slideOut animation for flash messages
const style = document.createElement("style");
style.textContent = `
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Admin Sidebar Toggle
function toggleSidebar() {
  const sidebar = document.getElementById("adminSidebar");
  const sidebarToggle = document.getElementById("sidebarToggle");

  if (sidebar) {
    sidebar.classList.toggle("active");
    sidebar.classList.toggle("collapsed");
  }
}

// Close modal
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("active");
  }
}

// Open modal
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("active");
  }
}

// Close modal when clicking outside
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("modal-overlay")) {
    e.target.classList.remove("active");
  }
});

// Admin Header Scroll Effect
let lastScroll = 0;
window.addEventListener("scroll", function () {
  const adminHeader = document.querySelector(".admin-header");
  if (adminHeader) {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 10) {
      adminHeader.style.boxShadow = "0 4px 16px rgba(0, 0, 0, 0.08)";
    } else {
      adminHeader.style.boxShadow = "0 2px 12px rgba(0, 0, 0, 0.04)";
    }

    lastScroll = currentScroll;
  }
});
