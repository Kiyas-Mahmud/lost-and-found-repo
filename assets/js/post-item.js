/**
 * Post Item Form Handler
 * Handles form submission, validation, and image upload for posting lost/found items
 */

document.addEventListener("DOMContentLoaded", function () {
  console.log("Post item script loaded");
  loadCategories();
  loadLocations();
  setupImageUpload();
  setupFormSubmission();
});

// Load categories from API
async function loadCategories() {
  try {
    const response = await apiGet("../../api/public/categories.php");
    if (response.success) {
      const select = document.getElementById("category");
      response.data.forEach((cat) => {
        const option = document.createElement("option");
        option.value = cat.category_id;
        option.textContent = cat.category_name;
        select.appendChild(option);
      });
    }
  } catch (error) {
    console.error("Failed to load categories:", error);
  }
}

// Load locations from API
async function loadLocations() {
  try {
    const response = await apiGet("../../api/public/locations.php");
    if (response.success) {
      const select = document.getElementById("location");
      response.data.forEach((loc) => {
        const option = document.createElement("option");
        option.value = loc.location_id;
        option.textContent = loc.location_name;
        select.appendChild(option);
      });
    }
  } catch (error) {
    console.error("Failed to load locations:", error);
  }
}

// Setup image upload handling
function setupImageUpload() {
  const imageInput = document.getElementById("image");
  const uploadArea = document.getElementById("imageUploadArea");
  const preview = document.getElementById("imagePreview");
  const previewImage = document.getElementById("previewImage");
  const removeBtn = document.getElementById("removeImageBtn");
  const placeholder = uploadArea.querySelector(".upload-placeholder");

  // Click to upload
  uploadArea.addEventListener("click", function (e) {
    if (!e.target.closest(".remove-image-btn")) {
      imageInput.click();
    }
  });

  // File selection
  imageInput.addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      validateAndPreviewImage(file);
    }
  });

  // Drag and drop
  uploadArea.addEventListener("dragover", function (e) {
    e.preventDefault();
    uploadArea.classList.add("drag-over");
  });

  uploadArea.addEventListener("dragleave", function () {
    uploadArea.classList.remove("drag-over");
  });

  uploadArea.addEventListener("drop", function (e) {
    e.preventDefault();
    uploadArea.classList.remove("drag-over");

    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith("image/")) {
      imageInput.files = e.dataTransfer.files;
      validateAndPreviewImage(file);
    }
  });

  // Remove image
  removeBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    imageInput.value = "";
    preview.style.display = "none";
    placeholder.style.display = "flex";
    document.getElementById("imageError").textContent = "";
  });
}

// Validate and preview image
function validateAndPreviewImage(file) {
  const errorElement = document.getElementById("imageError");
  const preview = document.getElementById("imagePreview");
  const previewImage = document.getElementById("previewImage");
  const placeholder = document.querySelector(".upload-placeholder");

  // Clear previous errors
  errorElement.textContent = "";

  // Validate file type
  const validTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"];
  if (!validTypes.includes(file.type)) {
    errorElement.textContent =
      "Please upload a valid image file (JPG, PNG, or WEBP)";
    document.getElementById("image").value = "";
    return;
  }

  // Validate file size (5MB)
  const maxSize = 5 * 1024 * 1024; // 5MB in bytes
  if (file.size > maxSize) {
    errorElement.textContent = "Image size must be less than 5MB";
    document.getElementById("image").value = "";
    return;
  }

  // Show preview
  const reader = new FileReader();
  reader.onload = function (e) {
    previewImage.src = e.target.result;
    preview.style.display = "block";
    placeholder.style.display = "none";
  };
  reader.readAsDataURL(file);
}

// Setup form submission
function setupFormSubmission() {
  const form = document.getElementById("postItemForm");

  console.log("Setting up form submission, form:", form);

  if (!form) {
    console.error("Form with id 'postItemForm' not found!");
    return;
  }

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    console.log("Form submitted");

    // Validate form
    if (!validateForm()) {
      console.log("Form validation failed");
      return;
    }

    console.log("Form validation passed");

    // Disable submit button
    const submitBtn = document.getElementById("submitBtn");
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Posting...';

    try {
      // Create FormData object
      const formData = new FormData(form);

      // Submit form
      const response = await fetch("../../api/student/post_item.php", {
        method: "POST",
        body: formData,
      });

      // Check if response is ok
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      // Get response text first for debugging
      const text = await response.text();
      console.log("Response:", text);

      let result;
      try {
        result = JSON.parse(text);
      } catch (e) {
        console.error("JSON Parse Error:", e);
        throw new Error(
          "Server returned invalid JSON. Check console for details.",
        );
      }

      if (result.success) {
        showAlert("success", result.message || "Item posted successfully!");
        setTimeout(() => {
          window.location.href = "my_posts.php";
        }, 1500);
      } else {
        showAlert(
          "error",
          result.message || "Failed to post item. Please try again.",
        );
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    } catch (error) {
      console.error("Error:", error);
      showAlert("error", "An error occurred: " + error.message);
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
}

// Validate form
function validateForm() {
  let isValid = true;

  // Clear previous errors
  document
    .querySelectorAll(".error-message")
    .forEach((el) => (el.textContent = ""));

  // Validate title
  const title = document.getElementById("title").value.trim();
  if (!title) {
    document.getElementById("titleError").textContent = "Title is required";
    isValid = false;
  } else if (title.length < 3) {
    document.getElementById("titleError").textContent =
      "Title must be at least 3 characters";
    isValid = false;
  }

  // Validate description
  const description = document.getElementById("description").value.trim();
  if (!description) {
    document.getElementById("descriptionError").textContent =
      "Description is required";
    isValid = false;
  } else if (description.length < 10) {
    document.getElementById("descriptionError").textContent =
      "Description must be at least 10 characters";
    isValid = false;
  }

  // Validate category
  const category = document.getElementById("category").value;
  if (!category) {
    document.getElementById("categoryError").textContent =
      "Please select a category";
    isValid = false;
  }

  // Validate location
  const location = document.getElementById("location").value;
  if (!location) {
    document.getElementById("locationError").textContent =
      "Please select a location";
    isValid = false;
  }

  // Validate event date
  const eventDate = document.getElementById("eventDate").value;
  if (!eventDate) {
    document.getElementById("eventDateError").textContent =
      "Please select a date";
    isValid = false;
  } else {
    const selectedDate = new Date(eventDate);
    const today = new Date();
    selectedDate.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    if (selectedDate > today) {
      document.getElementById("eventDateError").textContent =
        "Date cannot be in the future";
      isValid = false;
    }
  }

  return isValid;
}

// Show alert message
function showAlert(type, message) {
  const alertElement = document.getElementById("alertMessage");
  alertElement.className = `alert alert-${type}`;
  alertElement.textContent = message;
  alertElement.style.display = "block";

  // Scroll to top
  window.scrollTo({ top: 0, behavior: "smooth" });

  // Auto hide success message
  if (type === "success") {
    setTimeout(() => {
      alertElement.style.display = "none";
    }, 3000);
  }
}
