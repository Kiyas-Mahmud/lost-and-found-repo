/**
 * My Claims Page JavaScript
 * Handles loading, filtering, and managing user's claims
 */

let currentFilter = "";
let claimToCancel = null;

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  loadMyClaims();
  setupFilters();
});

/**
 * Load user's claims
 */
async function loadMyClaims() {
  const container = document.getElementById("claimsContainer");
  const emptyState = document.getElementById("emptyState");

  // Show loading
  container.innerHTML =
    '<div class="loading-container"><div class="spinner"></div><p>Loading your claims...</p></div>';
  emptyState.style.display = "none";

  try {
    const params = new URLSearchParams();
    if (currentFilter) params.append("status", currentFilter);

    const url = `/api/student/my_claims.php?${params.toString()}`;
    const response = await apiGet(url);

    if (response.success) {
      const claims = response.data.claims;

      if (claims.length === 0) {
        container.innerHTML = "";
        emptyState.style.display = "block";
      } else {
        emptyState.style.display = "none";
        displayClaims(claims);
      }
    } else {
      showAlert(response.message || "Failed to load claims", "error");
      container.innerHTML =
        '<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' +
        (response.message || "Failed to load claims. Please try again.") +
        "</div>";
      emptyState.style.display = "none";
    }
  } catch (error) {
    console.error("Error loading claims:", error);
    showAlert("An error occurred while loading claims", "error");
    container.innerHTML =
      '<div class="error-message"><i class="fas fa-exclamation-circle"></i> An error occurred. Please refresh the page.</div>';
    emptyState.style.display = "none";
  }
}

/**
 * Display claims in grid layout
 */
function displayClaims(claims) {
  const container = document.getElementById("claimsContainer");

  const claimsHTML = `
        <div class="claims-grid">
            ${claims.map((claim) => createClaimCard(claim)).join("")}
        </div>
    `;

  container.innerHTML = claimsHTML;
}

/**
 * Create HTML for a single claim card
 */
function createClaimCard(claim) {
  const imagePath = claim.image_path ? `../../${claim.image_path}` : null;
  const statusClass = claim.claim_status.toLowerCase();

  // Format dates with time
  const claimDate = new Date(claim.created_at);
  const now = new Date();
  const diffTime = Math.abs(now - claimDate);
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
  const diffHours = Math.floor(diffTime / (1000 * 60 * 60));

  let timeAgo;
  if (diffHours < 24) {
    timeAgo = `${diffHours} hour${diffHours !== 1 ? "s" : ""} ago`;
  } else if (diffDays < 7) {
    timeAgo = `${diffDays} day${diffDays !== 1 ? "s" : ""} ago`;
  } else {
    timeAgo = claimDate.toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  const adminNotesSection = claim.admin_note
    ? `
        <div class="admin-response ${
          statusClass === "rejected" ? "rejected" : ""
        }">
            <div class="admin-response-label">Admin Response</div>
            <div class="admin-response-text">${escapeHtml(
              claim.admin_note,
            )}</div>
        </div>
    `
    : "";

  const cancelButton =
    claim.claim_status === "PENDING"
      ? `
        <button class="btn btn-warning" onclick="openCancelModal(${
          claim.claim_id
        }, '${escapeHtml(claim.title)}')">
            <i class="fas fa-ban"></i> Cancel Claim
        </button>
    `
      : "";

  return `
        <div class="claim-card">
            <div class="claim-card-image">
                ${
                  imagePath
                    ? `<img src="${imagePath}" alt="${escapeHtml(claim.title)}">`
                    : `<div class="no-image"><i class="fas fa-image"></i></div>`
                }
                <div class="claim-card-badges">
                    <span class="badge badge-${claim.item_type.toLowerCase()}">
                        <i class="fas fa-${claim.item_type === "LOST" ? "search" : "hand-holding"}"></i>
                        ${claim.item_type}
                    </span>
                    <span class="badge badge-claim-status badge-${statusClass}">
                        <i class="fas fa-${statusClass === "pending" ? "clock" : statusClass === "approved" ? "check-circle" : "times-circle"}"></i>
                        ${claim.claim_status}
                    </span>
                </div>
            </div>
            
            <div class="claim-card-content">
                <div class="claim-card-header">
                    <h3 class="claim-card-title">${escapeHtml(claim.title)}</h3>
                    <span class="claim-time">
                        <i class="far fa-clock"></i> ${timeAgo}
                    </span>
                </div>
                
                <div class="claim-card-info">
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span>${escapeHtml(claim.category_name || "Uncategorized")}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${escapeHtml(claim.location_name || "Unknown")}</span>
                    </div>
                </div>
                
                ${
                  claim.proof_answer_1 || claim.proof_answer_2
                    ? `
                    <div class="claim-proof-section">
                        <div class="proof-header">
                            <i class="fas fa-shield-alt"></i>
                            <span>Proof Submitted</span>
                        </div>
                        ${claim.proof_answer_1 ? `<p class="proof-item"><strong>Q1:</strong> ${escapeHtml(claim.proof_answer_1)}</p>` : ""}
                        ${claim.proof_answer_2 ? `<p class="proof-item"><strong>Q2:</strong> ${escapeHtml(claim.proof_answer_2)}</p>` : ""}
                    </div>
                `
                    : ""
                }
                
                ${
                  claim.admin_note
                    ? `
                    <div class="admin-note-section ${statusClass === "rejected" ? "rejected" : "approved"}">
                        <div class="note-header">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin Response</span>
                        </div>
                        <p class="note-text">${escapeHtml(claim.admin_note)}</p>
                    </div>
                `
                    : ""
                }
                
                <div class="claim-card-actions">
                    <button class="btn btn-info" onclick="viewClaimDetails(${claim.claim_id}, ${JSON.stringify(claim).replace(/"/g, "&quot;")})">
                        <i class="fas fa-info-circle"></i>
                        <span>Details</span>
                    </button>
                    ${
                      claim.claim_status === "PENDING"
                        ? `
                        <button class="btn btn-warning" onclick="openCancelModal(${claim.claim_id}, '${escapeHtml(claim.title)}')">
                            <i class="fas fa-ban"></i>
                            <span>Cancel</span>
                        </button>
                    `
                        : ""
                    }
                </div>
            </div>
        </div>
    `;
}

/**
 * Setup filter event listeners
 */
function setupFilters() {
  const chipButtons = document.querySelectorAll(".filter-chips .chip");
  chipButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const filterValue = this.dataset.value;

      // Update active state
      chipButtons.forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");

      // Update filter and reload
      currentFilter = filterValue;
      loadMyClaims();
    });
  });
}

/**
 * Open cancel claim modal
 */
function openCancelModal(claimId, itemTitle) {
  claimToCancel = claimId;
  document.getElementById("cancelItemTitle").textContent = `Item: ${itemTitle}`;
  document.getElementById("cancelModal").classList.add("show");
  document.body.style.overflow = "hidden";
}

/**
 * Close cancel modal
 */
function closeCancelModal() {
  claimToCancel = null;
  document.getElementById("cancelModal").classList.remove("show");
  document.body.style.overflow = "";
}

/**
 * Confirm and execute cancel
 */
async function confirmCancel() {
  if (!claimToCancel) return;

  try {
    const formData = new FormData();
    formData.append("claim_id", claimToCancel);

    const response = await apiPost("/api/student/cancel_claim.php", formData);

    if (response.success) {
      showAlert("Claim cancelled successfully", "success");
      closeCancelModal();
      loadMyClaims();
    } else {
      showAlert(response.message || "Failed to cancel claim", "error");
    }
  } catch (error) {
    console.error("Error cancelling claim:", error);
    showAlert("An error occurred while cancelling the claim", "error");
  }
}

/**
 * View claim details in modal
 */
function viewClaimDetails(claimId, claim) {
  const eventDate = new Date(claim.event_date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

  const claimDate = new Date(claim.claim_date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });

  const reviewedDate = claim.reviewed_at
    ? new Date(claim.reviewed_at).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      })
    : "Not yet reviewed";

  const imagePath = claim.image_path ? `../../${claim.image_path}` : null;

  const detailsHTML = `
        <div style="display: grid; grid-template-columns: ${imagePath ? "280px 1fr" : "1fr"}; gap: 24px;">
            ${
              imagePath
                ? `
                <div style="flex-shrink: 0;">
                    <img src="${imagePath}" alt="${escapeHtml(
                      claim.title,
                    )}" style="width: 100%; height: auto; max-height: 350px; object-fit: cover; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                </div>
            `
                : ""
            }
            
            <div style="min-width: 0;">
                <h4 style="margin: 0 0 16px 0; color: var(--text-primary); font-size: 1.3rem;">${escapeHtml(
                  claim.title,
                )}</h4>
                
                <div style="display: grid; gap: 14px;">
                    <div>
                        <strong style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Description:</strong>
                        <p style="margin-top: 6px; color: var(--text-primary); line-height: 1.5; font-size: 0.95rem;">${escapeHtml(
                          claim.description,
                        )}</p>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; padding: 12px; background: var(--bg-light); border-radius: 8px;">
                        <div>
                            <strong style="color: var(--text-muted); font-size: 0.8rem;">Type:</strong>
                            <p style="margin-top: 2px; font-size: 0.9rem; font-weight: 500;">${claim.item_type}</p>
                        </div>
                        <div>
                            <strong style="color: var(--text-muted); font-size: 0.8rem;">Category:</strong>
                            <p style="margin-top: 2px; font-size: 0.9rem; font-weight: 500;">${escapeHtml(
                              claim.category_name || "Uncategorized",
                            )}</p>
                        </div>
                        <div>
                            <strong style="color: var(--text-muted); font-size: 0.8rem;">Location:</strong>
                            <p style="margin-top: 2px; font-size: 0.9rem; font-weight: 500;">${escapeHtml(
                              claim.location_name || "Unknown",
                            )}</p>
                        </div>
                        <div>
                            <strong style="color: var(--text-muted); font-size: 0.8rem;">Event Date:</strong>
                            <p style="margin-top: 2px; font-size: 0.9rem; font-weight: 500;">${eventDate}</p>
                        </div>
                    </div>
                    
                    <div style="padding: 12px; background: #f0f9ff; border-radius: 8px; border-left: 3px solid #0284c7;">
                        <strong style="color: var(--text-muted); font-size: 0.8rem;">Posted By:</strong>
                        <p style="margin-top: 4px; font-size: 0.95rem; font-weight: 500;">${escapeHtml(
                          claim.poster_name,
                        )}</p>
                    </div>
                    
                    <div style="border-top: 2px solid var(--border-color); padding-top: 14px;">
                        <strong style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Your Proof of Ownership:</strong>
                        <p style="margin-top: 8px; color: var(--text-primary); line-height: 1.6; font-size: 0.95rem; background: #fef3c7; padding: 10px; border-radius: 6px;">${escapeHtml(
                          claim.proof_description || "No proof provided",
                        )}</p>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                        <div>
                            <strong style="color: var(--text-muted); font-size: 0.8rem;">Claim Date:</strong>
                            <p style="margin-top: 4px; font-size: 0.85rem;">${claimDate}</p>
                        </div>
                        <div>
                            <strong style="color: var(--text-muted); font-size: 0.8rem;">Reviewed At:</strong>
                            <p style="margin-top: 4px; font-size: 0.85rem;">${reviewedDate}</p>
                        </div>
                    </div>
                    
                    ${
                      claim.admin_notes
                        ? `
                        <div style="background: ${
                          claim.claim_status === "REJECTED"
                            ? "#FEF2F2"
                            : "#F0FDF4"
                        }; padding: 14px; border-radius: 8px; border-left: 3px solid ${
                          claim.claim_status === "REJECTED"
                            ? "#DC2626"
                            : "#16A34A"
                        };">
                            <strong style="color: var(--text-primary); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Admin Response:</strong>
                            <p style="margin-top: 8px; color: var(--text-primary); line-height: 1.6; font-size: 0.95rem;">${escapeHtml(
                              claim.admin_notes,
                            )}</p>
                        </div>
                    `
                        : ""
                    }
                </div>
            </div>
        </div>
    `;

  document.getElementById("claimDetailsContent").innerHTML = detailsHTML;
  document.getElementById("detailsModal").classList.add("show");
  document.body.style.overflow = "hidden";
}

/**
 * Close details modal
 */
function closeDetailsModal() {
  document.getElementById("detailsModal").classList.remove("show");
  document.body.style.overflow = "";
}

/**
 * Show alert message
 */
function showAlert(message, type = "info") {
  const container = document.getElementById("alertContainer");
  const alertClass = type === "success" ? "alert-success" : "alert-error";
  const icon = type === "success" ? "check-circle" : "exclamation-circle";

  const alertHTML = `
        <div class="alert ${alertClass}" role="alert">
            <i class="fas fa-${icon}"></i>
            <span>${escapeHtml(message)}</span>
        </div>
    `;

  container.innerHTML = alertHTML;

  // Auto-hide after 5 seconds
  setTimeout(() => {
    container.innerHTML = "";
  }, 5000);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

// Close modals when clicking overlay
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("modal-overlay")) {
    closeCancelModal();
    closeDetailsModal();
  }
});
