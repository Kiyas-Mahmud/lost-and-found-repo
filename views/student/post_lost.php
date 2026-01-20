<?php
// Set page variable for navbar
$page = 'post_lost';

// Start session and check authentication
require_once '../../config/helpers.php';
require_once '../../config/session.php';

// Require student authentication
requireStudent();

$userName = $_SESSION['full_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Lost Item - Lost & Found</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        const BASE_URL = 'http://localhost:88/lost-and-found';
    </script>
</head>
<body>
    <?php include '../components/common/navbar_student.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div class="page-header">
                <div>
                    <h1><i class="fas fa-exclamation-circle text-danger"></i> Report Lost Item</h1>
                    <p>Help us find your lost item by providing detailed information</p>
                </div>
            </div>

            <div class="post-item-container">
                <form id="postItemForm" class="post-item-form" enctype="multipart/form-data">
                    <input type="hidden" name="item_type" value="LOST">
                    
                    <!-- Alert Messages -->
                    <div id="alertMessage" class="alert" style="display: none;"></div>

                    <!-- Item Basic Information -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-info-circle"></i> Basic Information
                        </h2>

                        <div class="form-group">
                            <label for="title" class="required">Item Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="form-control" 
                                placeholder="e.g., Blue Water Bottle, Black Backpack"
                                required
                                maxlength="200">
                            <span class="error-message" id="titleError"></span>
                        </div>

                        <div class="form-group">
                            <label for="description" class="required">Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="form-control" 
                                rows="5"
                                placeholder="Provide detailed description including brand, color, size, distinguishing features..."
                                required></textarea>
                            <span class="error-message" id="descriptionError"></span>
                            <small class="form-text">Be as detailed as possible to help identify your item</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category" class="required">Category</label>
                                <select id="category" name="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                </select>
                                <span class="error-message" id="categoryError"></span>
                            </div>

                            <div class="form-group">
                                <label for="location" class="required">Location Where Lost</label>
                                <select id="location" name="location_id" class="form-control" required>
                                    <option value="">Select Location</option>
                                </select>
                                <span class="error-message" id="locationError"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="eventDate" class="required">When Did You Lose It?</label>
                            <input 
                                type="date" 
                                id="eventDate" 
                                name="event_date" 
                                class="form-control"
                                max="<?php echo date('Y-m-d'); ?>"
                                required>
                            <span class="error-message" id="eventDateError"></span>
                            <small class="form-text">Date when you lost the item</small>
                        </div>
                    </div>

                    <!-- Item Image -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-image"></i> Item Image
                        </h2>

                        <div class="form-group">
                            <label for="image">Upload Image</label>
                            <div class="image-upload-wrapper">
                                <div class="image-upload-area" id="imageUploadArea">
                                    <input 
                                        type="file" 
                                        id="image" 
                                        name="image" 
                                        accept="image/jpeg,image/jpg,image/png,image/webp"
                                        hidden>
                                    <div class="upload-placeholder">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload or drag and drop</p>
                                        <small>JPG, PNG or WEBP (Max 5MB)</small>
                                    </div>
                                    <div class="image-preview" id="imagePreview" style="display: none;">
                                        <img src="" alt="Preview" id="previewImage">
                                        <button type="button" class="remove-image-btn" id="removeImageBtn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <span class="error-message" id="imageError"></span>
                                <small class="form-text">Optional: Upload a clear photo of the item (helps in identification)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-phone"></i> Contact Information
                        </h2>

                        <div class="form-group">
                            <label for="contactInfo">Contact Details (Optional)</label>
                            <textarea 
                                id="contactInfo" 
                                name="contact_info" 
                                class="form-control" 
                                rows="2"
                                placeholder="Additional contact information (email, phone, room number)"></textarea>
                            <small class="form-text">Your name and student ID will be automatically attached</small>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" onclick="window.location.href='browse.php'" class="btn btn-outline btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-paper-plane"></i> Post Lost Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script src="../../assets/js/post-item.js"></script>
</body>
</html>
