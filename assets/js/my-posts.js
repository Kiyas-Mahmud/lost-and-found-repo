/**
 * My Posts Page JavaScript
 * Handles loading, filtering, and managing user's posted items
 */

let currentFilters = {
  type: "",
  status: "",
};

let itemToDelete = null;
let itemToUpdate = null;

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  loadMyPosts();
  setupFilters();
});

/**
 * Load user's posted items
 */
async function loadMyPosts() {
  const container = document.getElementById("postsContainer");
  const emptyState = document.getElementById("emptyState");

  // Show loading
  container.innerHTML =
    '<div class="loading-container"><div class="spinner"></div><p>Loading your posts...</p></div>';
  emptyState.style.display = "none";

  try {
    const params = new URLSearchParams();
    if (currentFilters.type) params.append("type", currentFilters.type);
    if (currentFilters.status) params.append("status", currentFilters.status);

    const url = `/api/student/my_posts.php?${params.toString()}`;
    const response = await apiGet(url);

    if (response.success) {
      const items = response.data.items;

      if (items.length === 0) {
        container.innerHTML = "";
        emptyState.style.display = "block";
      } else {
        emptyState.style.display = "none";
        displayPosts(items);
      }
    } else {
      showAlert(response.message || "Failed to load posts", "error");
      container.innerHTML =
        '<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' +
        (response.message || "Failed to load posts. Please try again.") +
        "</div>";
      emptyState.style.display = "none";
    }
  } catch (error) {
    console.error("Error loading posts:", error);
    showAlert("An error occurred while loading posts", "error");
    container.innerHTML =
      '<div class="error-message"><i class="fas fa-exclamation-circle"></i> An error occurred. Please refresh the page.</div>';
    emptyState.style.display = "none";
  }
}

/**
 * Display posts in grid layout
 */
function displayPosts(items) {
  const container = document.getElementById("postsContainer");

  const postsHTML = `
        <div class="posts-grid">
            ${items.map((item) => createPostCard(item)).join("")}
        </div>
    `;

  container.innerHTML = postsHTML;
}

/**
 * Create HTML for a single post card
 */
function createPostCard(item) {
  const imagePath = item.image_path ? `../../${item.image_path}` : null;
  const typeClass = item.item_type.toLowerCase();
  const statusClass = item.current_status.toLowerCase().replace("_", "-");

  // Format dates with time
  const eventDate = new Date(item.event_date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });

  const createdDate = new Date(item.created_at);
  const now = new Date();
  const diffTime = Math.abs(now - createdDate);
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
  const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
  const diffMinutes = Math.floor(diffTime / (1000 * 60));

  let timeAgo;
  if (diffMinutes < 1) {
    timeAgo = "Just now";
  } else if (diffMinutes < 60) {
    timeAgo = `${diffMinutes} min${diffMinutes !== 1 ? "s" : ""} ago`;
  } else if (diffHours < 24) {
    timeAgo = `${diffHours} hour${diffHours !== 1 ? "s" : ""} ago`;
  } else if (diffDays < 7) {
    timeAgo = `${diffDays} day${diffDays !== 1 ? "s" : ""} ago`;
  } else {
    timeAgo = createdDate.toLocaleDateString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    });
  }

  return `
        <div class="post-card">
            <div class="post-card-image">
                ${
                  imagePath
                    ? `<img src="${imagePath}" alt="${escapeHtml(item.title)}">`
                    : `<div class="no-image"><i class="fas fa-image"></i></div>`
                }
                <div class="post-card-badges">
                    <span class="badge badge-${typeClass}">
                        <i class="fas fa-${item.item_type === "LOST" ? "search" : "hand-holding"}"></i>
                        ${item.item_type}
                    </span>
                    <span class="badge badge-status badge-${statusClass}">
                        <i class="fas fa-circle"></i>
                        ${formatStatus(item.current_status)}
                    </span>
                </div>
            </div>
            
            <div class="post-card-content">
                <div class="post-card-header">
                    <h3 class="post-card-title">${escapeHtml(item.title)}</h3>
                    <span class="post-time">
                        <i class="far fa-clock"></i> ${timeAgo}
                    </span>
                </div>
                
                <div class="post-card-info">
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span>${escapeHtml(item.category_name || "Uncategorized")}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${escapeHtml(item.location_name || "Unknown")}</span>
                    </div>
                    <div class="info-item">
                        <i class="far fa-calendar"></i>
                        <span>${eventDate}</span>
                    </div>
                </div>
                
                <div class="post-card-actions">
                    <button class="btn btn-update" onclick="openStatusModal(${item.item_id}, '${escapeHtml(item.title)}', '${item.current_status}')">
                        <i class="fas fa-edit"></i>
                        <span>Update</span>
                    </button>
                    <button class="btn btn-delete" onclick="openDeleteModal(${item.item_id}, '${escapeHtml(item.title)}')">
                        <i class="fas fa-trash-alt"></i>
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
    `;
}

/**
 * Format status text for display
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
 * Setup filter event listeners
 */
function setupFilters() {
  // Type filter chips
  const chipButtons = document.querySelectorAll(".filter-chips .chip");
  chipButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const filterType = this.dataset.filter;
      const filterValue = this.dataset.value;

      // Update active state
      chipButtons.forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");

      // Update filter and reload
      currentFilters[filterType] = filterValue;
      loadMyPosts();
    });
  });

  // Status filter dropdown
  const statusFilter = document.getElementById("statusFilter");
  if (statusFilter) {
    statusFilter.addEventListener("change", function () {
      currentFilters.status = this.value;
      loadMyPosts();
    });
  }
}

/**
 * Open delete confirmation modal
 */
function openDeleteModal(itemId, itemTitle) {
  itemToDelete = itemId;
  document.getElementById("deleteItemTitle").textContent = itemTitle;
  document.getElementById("deleteModal").classList.add("show");
  document.body.style.overflow = "hidden";
}

/**
 * Close delete modal
 */
function closeDeleteModal() {
  itemToDelete = null;
  document.getElementById("deleteModal").classList.remove("show");
  document.body.style.overflow = "";
}

/**
 * Confirm and execute delete
 */
async function confirmDelete() {
  if (!itemToDelete) return;

  try {
    const formData = new FormData();
    formData.append("item_id", itemToDelete);

    const response = await apiPost("/api/student/delete_item.php", formData);

    if (response.success) {
      showAlert("Item deleted successfully", "success");
      closeDeleteModal();
      loadMyPosts(); // Reload the list
    } else {
      showAlert(response.message || "Failed to delete item", "error");
    }
  } catch (error) {
    console.error("Error deleting item:", error);
    showAlert("An error occurred while deleting the item", "error");
  }
}

/**
 * Open status update modal
 */
function openStatusModal(itemId, itemTitle, currentStatus) {
  itemToUpdate = itemId;
  document.getElementById("statusItemTitle").textContent = itemTitle;
  document.getElementById("newStatus").value = currentStatus;
  document.getElementById("statusModal").classList.add("show");
  document.body.style.overflow = "hidden";
}

/**
 * Close status modal
 */
function closeStatusModal() {
  itemToUpdate = null;
  document.getElementById("statusModal").classList.remove("show");
  document.body.style.overflow = "";
}

/**
 * Confirm and execute status update
 */
async function confirmStatusUpdate() {
  if (!itemToUpdate) return;

  const newStatus = document.getElementById("newStatus").value;

  try {
    const formData = new FormData();
    formData.append("item_id", itemToUpdate);
    formData.append("status", newStatus);

    const response = await apiPost("/api/student/update_status.php", formData);

    if (response.success) {
      showAlert("Status updated successfully", "success");
      closeStatusModal();
      loadMyPosts(); // Reload the list
    } else {
      showAlert(response.message || "Failed to update status", "error");
    }
  } catch (error) {
    console.error("Error updating status:", error);
    showAlert("An error occurred while updating the status", "error");
  }
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
    closeDeleteModal();
    closeStatusModal();
  }
});
