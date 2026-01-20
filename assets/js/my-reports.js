/**
 * My Reports Page JavaScript
 */

let currentStatus = "all";

// Load reports when page loads
document.addEventListener("DOMContentLoaded", function () {
  loadReports("all");
});

/**
 * Load reports from API
 */
async function loadReports(status = "all") {
  currentStatus = status;
  const loadingState = document.getElementById("loadingState");
  const reportsContainer = document.getElementById("reportsContainer");
  const emptyState = document.getElementById("emptyState");

  try {
    // Show loading
    loadingState.style.display = "block";
    reportsContainer.innerHTML = "";
    emptyState.style.display = "none";

    // Fetch reports
    const url =
      status === "all"
        ? "../../api/student/my_reports.php"
        : `../../api/student/my_reports.php?status=${status}`;

    const response = await apiGet(url);

    // Hide loading
    loadingState.style.display = "none";

    if (response.success) {
      const reports = response.data.reports || [];

      if (reports.length === 0) {
        emptyState.style.display = "block";
      } else {
        renderReports(reports);
      }
    } else {
      showAlert("error", response.message || "Failed to load reports");
    }
  } catch (error) {
    loadingState.style.display = "none";
    console.error("Error loading reports:", error);
    showAlert("error", "An error occurred while loading reports");
  }
}

/**
 * Render reports to the page
 */
function renderReports(reports) {
  const container = document.getElementById("reportsContainer");
  container.innerHTML = "";

  reports.forEach((report) => {
    const reportCard = createReportCard(report);
    container.innerHTML += reportCard;
  });
}

/**
 * Create report card HTML
 */
function createReportCard(report) {
  const statusClass = report.report_status.toLowerCase();
  const statusIcon = getStatusIcon(report.report_status);
  const reasonLabel = formatReason(report.reason);
  const imagePath = report.image_path ? `../../${report.image_path}` : null;

  return `
        <div class="report-card">
            <div class="report-card-header">
                <div class="report-item-info">
                    ${
                      imagePath
                        ? `<img src="${imagePath}" alt="${report.title}" class="report-item-thumb">`
                        : `<div class="report-item-thumb no-image"><i class="fas fa-image"></i></div>`
                    }
                    <div>
                        <h3 class="report-item-title">${report.title}</h3>
                        <span class="badge badge-${report.item_type.toLowerCase()}">${report.item_type}</span>
                    </div>
                </div>
                <span class="badge badge-${statusClass}">
                    ${statusIcon} ${report.report_status}
                </span>
            </div>
            
            <div class="report-card-body">
                <div class="report-info-row">
                    <div class="report-info-item">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><strong>Reason:</strong> ${reasonLabel}</span>
                    </div>
                    <div class="report-info-item">
                        <i class="fas fa-calendar"></i>
                        <span><strong>Submitted:</strong> ${formatDate(report.created_at)}</span>
                    </div>
                </div>
                
                ${
                  report.comment
                    ? `
                    <div class="report-comment">
                        <i class="fas fa-comment"></i>
                        <p>${report.comment}</p>
                    </div>
                `
                    : ""
                }
                
                ${
                  report.admin_note
                    ? `
                    <div class="admin-note">
                        <i class="fas fa-user-shield"></i>
                        <strong>Admin Response:</strong>
                        <p>${report.admin_note}</p>
                    </div>
                `
                    : ""
                }
                
                ${
                  report.resolved_at
                    ? `
                    <div class="report-info-item">
                        <i class="fas fa-clock"></i>
                        <span><strong>Reviewed:</strong> ${formatDate(report.resolved_at)}</span>
                    </div>
                `
                    : ""
                }
            </div>
            
            <div class="report-card-footer">
                <a href="item_details.php?id=${report.item_id}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> View Item
                </a>
            </div>
        </div>
    `;
}

/**
 * Filter reports by status
 */
function filterReports(status, buttonElement) {
  // Update active tab
  document.querySelectorAll(".filter-tab").forEach((tab) => {
    tab.classList.remove("active");
  });
  buttonElement.classList.add("active");

  // Load reports
  loadReports(status);
}

/**
 * Get status icon
 */
function getStatusIcon(status) {
  const icons = {
    OPEN: '<i class="fas fa-clock"></i>',
    RESOLVED: '<i class="fas fa-check"></i>',
  };
  return icons[status] || '<i class="fas fa-flag"></i>';
}

/**
 * Format reason for display
 */
function formatReason(reason) {
  const reasons = {
    FAKE_POST: "Fake or Fraudulent Post",
    WRONG_INFO: "Incorrect Information",
    SPAM: "Spam or Duplicate",
    SUSPICIOUS_CLAIM: "Suspicious Claim",
    OTHER: "Other Issue",
  };
  return reasons[reason] || reason;
}

/**
 * Format date
 */
function formatDate(dateString) {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

/**
 * Show alert message
 */
function showAlert(type, message) {
  const container = document.getElementById("alertContainer");
  const alertClass = type === "success" ? "alert-success" : "alert-error";
  const iconClass = type === "success" ? "check-circle" : "exclamation-circle";

  const alert = document.createElement("div");
  alert.className = `alert ${alertClass}`;
  alert.innerHTML = `
        <i class="fas fa-${iconClass}"></i>
        <span>${message}</span>
    `;

  container.appendChild(alert);

  setTimeout(() => alert.remove(), 5000);
}
