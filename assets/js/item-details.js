/**
 * Item Details Page JavaScript
 */

let itemData = null;

document.addEventListener("DOMContentLoaded", function () {
  loadItemDetails();
  setupClaimForm();
  setupModalEvents();
});

/**
 * Load item details from API
 */
async function loadItemDetails() {
  const loadingState = document.getElementById("loadingState");
  const errorState = document.getElementById("errorState");
  const detailsContainer = document.getElementById("itemDetailsContainer");

  try {
    const response = await apiGet(`/api/public/item_details.php?id=${ITEM_ID}`);

    if (response.success && response.data) {
      itemData = response.data;
      loadingState.style.display = "none";
      errorState.style.display = "none";
      detailsContainer.style.display = "block";
      displayItemDetails(itemData);
    } else {
      loadingState.style.display = "none";
      errorState.style.display = "block";
    }
  } catch (error) {
    loadingState.style.display = "none";
    errorState.style.display = "block";
  }
}

/**
 * Display item details on the page
 */
function displayItemDetails(item) {
  const imagePath = item.image_path ? `../../${item.image_path}` : null;
  const typeClass = item.item_type.toLowerCase();

  const eventDate = new Date(item.event_date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  const postedDate = new Date(item.created_at).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  // Check if user can claim
  const canClaim = IS_LOGGED_IN && IS_STUDENT && item.current_status === "OPEN";

  const html = `
        <div class="item-detail-card">
            <div class="item-detail-image">
                ${
                  imagePath
                    ? `<img src="${imagePath}" alt="${item.title}">`
                    : `<div class="no-image-placeholder">
                         <i class="fas fa-image"></i>
                         <span>No Image Available</span>
                       </div>`
                }
                <span class="item-type-badge-absolute ${typeClass}">
                    <i class="fas fa-${
                      item.item_type === "LOST" ? "search" : "check-circle"
                    }"></i>
                    ${item.item_type}
                </span>
            </div>
            
            <div class="item-detail-content">
                <div class="item-detail-header">
                    <h1 class="item-detail-title">${item.title}</h1>
                    <span class="item-status-badge status-${item.current_status
                      .toLowerCase()
                      .replace("_", "-")}">
                        ${formatStatus(item.current_status)}
                    </span>
                </div>
                
                <div class="item-detail-meta">
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>${item.category_name || "Uncategorized"}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${item.location_name || "Unknown"}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>${eventDate}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>${item.poster_name}</span>
                    </div>
                </div>
                
                <div class="item-detail-description">
                    <h3><i class="fas fa-align-left"></i> Description</h3>
                    <p>${item.description}</p>
                </div>
                
                ${
                  item.contact_info
                    ? `
                    <div class="item-detail-contact">
                        <i class="fas fa-phone"></i>
                        <span>${item.contact_info}</span>
                    </div>
                `
                    : ""
                }
                
                <div class="item-detail-footer">
                    <span class="posted-time">
                        <i class="fas fa-clock"></i>
                        Posted ${postedDate}
                    </span>
                    ${
                      canClaim
                        ? `
                        <button class="btn btn-primary" onclick="openClaimModal()">
                            <i class="fas fa-hand-paper"></i> Claim This Item
                        </button>
                    `
                        : !IS_LOGGED_IN
                        ? `
                        <a href="../login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login to Claim
                        </a>
                    `
                        : `
                        <button class="btn btn-secondary" disabled>
                            <i class="fas fa-info-circle"></i> ${
                              item.current_status === "CLAIM_PENDING"
                                ? "Claim Pending"
                                : "Not Available"
                            }
                        </button>
                    `
                    }
                </div>
            </div>
        </div>
    `;

  document.getElementById("itemDetailsContainer").innerHTML = html;
}

/**
 * Format status text
 */
function formatStatus(status) {
  return status
    .replace(/_/g, " ")
    .toLowerCase()
    .split(" ")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(" ");
}

/**
 * Open claim modal
 */
function openClaimModal() {
  const modal = document.getElementById("claimModal");
  document.getElementById("claimItemId").value = ITEM_ID;
  document.getElementById("proofDescription").value = "";
  document.getElementById("claimAlertContainer").innerHTML = "";
  modal.classList.add("show");
  document.body.style.overflow = "hidden";
}

/**
 * Close claim modal
 */
function closeClaimModal() {
  const modal = document.getElementById("claimModal");
  modal.classList.remove("show");
  document.body.style.overflow = "";
}

/**
 * Setup modal events
 */
function setupModalEvents() {
  const modal = document.getElementById("claimModal");

  // Close on backdrop click
  modal.addEventListener("click", function (e) {
    if (e.target === modal) {
      closeClaimModal();
    }
  });

  // Close on escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && modal.classList.contains("show")) {
      closeClaimModal();
    }
  });
}

/**
 * Setup claim form submission
 */
function setupClaimForm() {
  const form = document.getElementById("claimForm");
  if (!form) return;

  form.addEventListener("submit", async function (e) {
    e.preventDefault();

    const proofDescription = document
      .getElementById("proofDescription")
      .value.trim();
    const alertContainer = document.getElementById("claimAlertContainer");

    // Validate
    if (proofDescription.length < 20) {
      showClaimAlert(
        "Please provide a more detailed proof (at least 20 characters)",
        "error"
      );
      return;
    }

    // Submit
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    try {
      const formData = new FormData();
      formData.append("item_id", ITEM_ID);
      formData.append("proof_description", proofDescription);

      const response = await apiPost("/api/student/submit_claim.php", formData);

      if (response.success) {
        showClaimAlert("Claim submitted successfully!", "success");
        setTimeout(() => {
          closeClaimModal();
          window.location.href = "my_claims.php";
        }, 2000);
      } else {
        showClaimAlert(response.message || "Failed to submit claim", "error");
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Claim';
      }
    } catch (error) {
      console.error("Error submitting claim:", error);
      showClaimAlert("An error occurred. Please try again.", "error");
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Claim';
    }
  });
}

/**
 * Show alert in claim modal
 */
function showClaimAlert(message, type) {
  const container = document.getElementById("claimAlertContainer");
  const alertClass = type === "success" ? "alert-success" : "alert-error";

  container.innerHTML = `
        <div class="alert ${alertClass}">
            <i class="fas fa-${
              type === "success" ? "check-circle" : "exclamation-circle"
            }"></i>
            <span>${message}</span>
        </div>
    `;
}
