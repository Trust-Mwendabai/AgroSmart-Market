<?php
session_start();
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'models/Product.php';
require_once 'models/Order.php';
require_once 'config/utils.php';

// Get current user information
$user = new User($GLOBALS['conn']);
$current_user = null;
if (isset($_SESSION['user_id'])) {
    $current_user = $user->get_user_by_id($_SESSION['user_id']);
}

// If user is not logged in or user data is not found, redirect to login
if (!isset($_SESSION['user_id']) || !$current_user) {
    header('Location: ../login.php');
    exit;
}

// Initialize all required fields with empty values if not set
$current_user['name'] = $current_user['name'] ?? '';
$current_user['email'] = $current_user['email'] ?? '';
$current_user['phone'] = $current_user['phone'] ?? '';
$current_user['location'] = $current_user['location'] ?? '';
$current_user['bio'] = $current_user['bio'] ?? '';

// Initialize farmer-specific fields if user is farmer
if ($current_user['user_type'] === 'farmer') {
    $current_user['nrc_number'] = $current_user['nrc_number'] ?? '';
    $current_user['literacy_level'] = $current_user['literacy_level'] ?? '';
}

// Check if user is active
if (!$current_user['is_active']) {
    header('Location: ../login.php?error=suspended');
    exit;
}

// Check if email is verified
if (!$current_user['email_verified']) {
    header('Location: ../verify-email.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the form data
    $errors = [];
    
    // Validate name
    if (empty($_POST['name'])) {
        $errors[] = "Name is required";
    }
    
    // Validate email
    if (empty($_POST['email'])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    // Validate phone (if provided)
    if (!empty($_POST['phone']) && !preg_match('/^[0-9+\-\s]{10,15}$/', $_POST['phone'])) {
        $errors[] = "<b>Warning</b>: Phone number should be 10-15 digits with optional +, - or spaces";
    }
    
    // For farmers, validate required fields
    if ($current_user['user_type'] === 'farmer') {
        if (empty($_POST['nrc_number'])) {
            $errors[] = "NRC Number is required for farmers";
        }
        if (empty($_POST['literacy_level'])) {
            $errors[] = "Literacy Level is required for farmers";
        }
    }
    
    // If there are validation errors
    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    } else {
        // Prepare data for update
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? null,
            'location' => $_POST['location'] ?? null,
            'bio' => $_POST['bio'] ?? null
        ];
        
        if ($current_user['user_type'] === 'farmer') {
            $data['nrc_number'] = $_POST['nrc_number'] ?? null;
            $data['literacy_level'] = $_POST['literacy_level'] ?? null;
        }
        
        $result = $user->update_profile($current_user['id'], $data);
        if (isset($result['error'])) {
            $error = $result['error'];
        } else {
            $success = "Profile updated successfully";
            $current_user = $user->get_user_by_id($current_user['id']);
        }
    }
}

// Check if farmer can sell products
$can_sell = true;
if (isset($current_user['user_type']) && $current_user['user_type'] === 'farmer') {
    $required_fields = ['name', 'email', 'phone', 'location', 'nrc_number', 'literacy_level'];
    foreach ($required_fields as $field) {
        if (!isset($current_user[$field]) || empty($current_user[$field])) {
            $can_sell = false;
            break;
        }
    }
}
?>
<?php
// Include header
include_once 'views/partials/header.php';
?>
<style>
    .profile-image-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    .profile-image-placeholder {
        width: 150px;
        height: 150px;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #6c757d;
        font-size: 3rem;
    }
    .user-stats {
        background-color: rgba(76, 175, 80, 0.1);
        border-left: 4px solid var(--primary-color);
        padding: 15px;
        margin-bottom: 20px;
    }
    .stat-item {
        text-align: center;
        padding: 10px;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    .stat-label {
        font-size: 0.85rem;
        color: #666;
    }
    .badge.bg-farmer {
        background-color: var(--primary-color);
    }
    .badge.bg-buyer {
        background-color: var(--secondary-color);
        color: #333;
    }
</style>


    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="profile-image-container mb-3 position-relative">
                            <?php
                            // Initialize placeholders for user's initials
                            $initials = '?';
                            if (!empty($current_user['name'])) {
                                $name_parts = explode(' ', trim($current_user['name']));
                                $initials = strtoupper(substr($name_parts[0], 0, 1));
                                if (count($name_parts) > 1) {
                                    $initials .= strtoupper(substr(end($name_parts), 0, 1));
                                }
                            }
                            
                            // Fix profile image path
                            $has_image = false;
                            $profile_image_path = '';
                            
                            if (!empty($current_user['profile_image'])) {
                                $profile_image_path = 'images/profiles/' . $current_user['profile_image'];
                                // Check if the file exists
                                if (file_exists($profile_image_path)) {
                                    $has_image = true;
                                }
                            }
                            
                            // Create directories if they don't exist
                            if (!file_exists('images')) {
                                mkdir('images', 0777, true);
                            }
                            if (!file_exists('images/profiles')) {
                                mkdir('images/profiles', 0777, true);
                            }
                            ?>
                            
                            <?php if ($has_image): ?>
                            <img src="<?php echo htmlspecialchars($profile_image_path); ?>" 
                                 alt="Profile Picture" 
                                 class="rounded-circle mb-3 img-thumbnail shadow" 
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                            <div class="profile-image-placeholder mb-3 shadow">
                                <?php echo htmlspecialchars($initials); ?>
                            </div>
                            <?php endif; ?>
                            <div class="mt-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                    <i class="fas fa-camera me-1"></i>Change Profile Picture
                                </button>
                            </div>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($current_user['name']); ?></h5>
                        <p class="card-text text-capitalize badge <?php echo $current_user['user_type'] === 'farmer' ? 'bg-farmer' : 'bg-buyer'; ?>">
                            <i class="fas <?php echo $current_user['user_type'] === 'farmer' ? 'fa-seedling' : 'fa-shopping-basket'; ?> me-1"></i>
                            <?php echo htmlspecialchars($current_user['user_type']); ?>
                        </p>
                        
                        <!-- User Stats Section -->
                        <?php 
                        // Get user stats based on user type
                        if ($current_user['user_type'] === 'farmer') {
                            // For farmers: Get product count and order count
                            $product_model = new Product($GLOBALS['conn']);
                            $order_model = new Order($GLOBALS['conn']);
                            $product_count = $product_model->count_farmer_products($current_user['id']);
                            $order_count = $order_model->count_farmer_orders($current_user['id']);
                            $stats = [
                                ['value' => $product_count, 'label' => 'Products'],
                                ['value' => $order_count, 'label' => 'Orders']
                            ];
                        } else {
                            // For buyers: Get order count and spent amount
                            $order_model = new Order($GLOBALS['conn']);
                            $order_count = $order_model->count_buyer_orders($current_user['id']);
                            $spent_amount = $order_model->get_buyer_total_spent($current_user['id']);
                            $stats = [
                                ['value' => $order_count, 'label' => 'Purchases'],
                                ['value' => 'ZMW ' . number_format($spent_amount, 2), 'label' => 'Total Spent']
                            ];
                        }
                        ?>
                        
                        <div class="user-stats my-3">
                            <div class="row">
                                <?php foreach ($stats as $stat): ?>
                                <div class="col-6 stat-item">
                                    <div class="stat-value"><?php echo $stat['value']; ?></div>
                                    <div class="stat-label"><?php echo $stat['label']; ?></div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="user-details mt-3 text-start">
                            <div class="mb-2">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <?php echo htmlspecialchars($current_user['email']); ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <?php if (!empty($current_user['phone'])): ?>
                                    <?php echo htmlspecialchars($current_user['phone']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <?php if (!empty($current_user['location'])): ?>
                                    <?php echo htmlspecialchars($current_user['location']); ?>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($current_user['bio'])): ?>
                            <div class="mt-3 p-2 bg-light rounded">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <small><?php echo nl2br(htmlspecialchars($current_user['bio'])); ?></small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Profile Display Card -->
                <div class="card shadow-sm mb-4" id="profile-display">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Details</h5>
                        <button type="button" class="btn btn-light btn-sm" id="edit-profile-btn">
                            <i class="fas fa-pencil-alt me-2"></i>Update Profile
                        </button>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; // No htmlspecialchars here to allow HTML in error messages ?></div>
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-user text-primary me-2"></i>Full Name</strong></p>
                                <p class="text-muted"><?php echo htmlspecialchars($current_user['name'] ?? 'Not provided'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-envelope text-primary me-2"></i>Email</strong></p>
                                <p class="text-muted"><?php echo htmlspecialchars($current_user['email'] ?? 'Not provided'); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-phone text-primary me-2"></i>Phone Number</strong></p>
                                <p class="text-muted"><?php echo !empty($current_user['phone']) ? htmlspecialchars($current_user['phone']) : 'Not provided'; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-map-marker-alt text-primary me-2"></i>Location</strong></p>
                                <p class="text-muted"><?php echo !empty($current_user['location']) ? htmlspecialchars($current_user['location']) : 'Not provided'; ?></p>
                            </div>
                        </div>
                        
                        <?php if (isset($current_user['user_type']) && $current_user['user_type'] === 'farmer'): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-id-card text-primary me-2"></i>NRC Number</strong></p>
                                <p class="text-muted"><?php echo !empty($current_user['nrc_number']) ? htmlspecialchars($current_user['nrc_number']) : 'Not provided'; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong><i class="fas fa-graduation-cap text-primary me-2"></i>Literacy Level</strong></p>
                                <p class="text-muted"><?php echo !empty($current_user['literacy_level']) ? htmlspecialchars($current_user['literacy_level']) : 'Not provided'; ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($current_user['bio'])): ?>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="fas fa-info-circle text-primary me-2"></i>Bio</strong></p>
                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($current_user['bio'])); ?></p>
                        </div>
                        <?php else: ?>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="fas fa-info-circle text-primary me-2"></i>Bio</strong></p>
                            <p class="text-muted">No bio provided</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Profile Edit Form -->
                <div class="card shadow-sm" id="profile-edit" style="display: none;">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h5>
                        <button type="button" class="btn btn-light btn-sm" id="view-profile-btn">
                            <i class="fas fa-eye me-2"></i>View Profile
                        </button>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Add CSRF token for security -->
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="name" name="name" 
                                            value="<?php echo htmlspecialchars($current_user['name']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email" 
                                            value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                            value="<?php echo htmlspecialchars($current_user['phone'] ?? ''); ?>" 
                                            pattern="[0-9+\- ]{10,15}" 
                                            title="Please enter a valid phone number (10-15 digits)">
                                    </div>
                                    <div class="form-text text-muted">Format: +260 (country code) followed by your number</div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" id="location" name="location" 
                                            value="<?php echo htmlspecialchars($current_user['location'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($current_user['user_type'] === 'farmer'): ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nrc_number" class="form-label">NRC Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                        <input type="text" class="form-control" id="nrc_number" name="nrc_number" 
                                            value="<?php echo htmlspecialchars($current_user['nrc_number'] ?? ''); ?>" required>
                                    </div>
                                    <small class="form-text text-muted">National Registration Card number</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="literacy_level" class="form-label">Literacy Level</label>
                                    <select class="form-select" id="literacy_level" name="literacy_level" required>
                                        <option value="" disabled <?php echo empty($current_user['literacy_level']) ? 'selected' : ''; ?>>Select Literacy Level</option>
                                        <option value="Illiterate" <?php echo isset($current_user['literacy_level']) && $current_user['literacy_level'] === 'Illiterate' ? 'selected' : ''; ?>>Illiterate</option>
                                        <option value="Basic" <?php echo isset($current_user['literacy_level']) && $current_user['literacy_level'] === 'Basic' ? 'selected' : ''; ?>>Basic</option>
                                        <option value="Primary" <?php echo isset($current_user['literacy_level']) && $current_user['literacy_level'] === 'Primary' ? 'selected' : ''; ?>>Primary</option>
                                        <option value="Secondary" <?php echo isset($current_user['literacy_level']) && $current_user['literacy_level'] === 'Secondary' ? 'selected' : ''; ?>>Secondary</option>
                                        <option value="Tertiary" <?php echo isset($current_user['literacy_level']) && $current_user['literacy_level'] === 'Tertiary' ? 'selected' : ''; ?>>Tertiary</option>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($current_user['bio'] ?? ''); ?></textarea>
                                <div class="form-text text-muted">Tell others about yourself or your farm</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($current_user['user_type']) && $current_user['user_type'] === 'farmer'): ?>
    <div class="container mt-4">
        <div class="alert <?php echo isset($can_sell) && $can_sell ? 'alert-success' : 'alert-warning'; ?>">
            <?php if (isset($can_sell) && $can_sell): ?>
                <p>Your profile is complete. You can now sell products.</p>
            <?php else: ?>
                <p>Please complete your profile by filling in all required fields before you can sell products.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Profile Image Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="uploadModalLabel"><i class="fas fa-camera me-2"></i>Upload Profile Picture</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="profileImageForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="modalProfileImage" class="form-label">Select Image</label>
                            <input type="file" class="form-control" id="modalProfileImage" name="profile_image" accept="image/*" required>
                            <small class="form-text text-muted">Maximum file size: 5MB. Supported formats: JPEG, PNG, GIF</small>
                        </div>
                        <div class="mb-3" id="imagePreviewContainer" style="display: none;">
                            <label class="form-label">Preview</label>
                            <div class="text-center">
                                <img id="imagePreview" src="#" alt="Preview" class="rounded img-fluid" style="max-height: 200px;">
                            </div>
                        </div>
                        <div class="alert alert-danger" id="uploadError" style="display: none;"></div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="uploadBtn">
                                <i class="fas fa-upload me-2"></i>Upload Image
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'views/partials/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile view/edit toggle functionality
        const profileDisplay = document.getElementById('profile-display');
        const profileEdit = document.getElementById('profile-edit');
        const editProfileBtn = document.getElementById('edit-profile-btn');
        const viewProfileBtn = document.getElementById('view-profile-btn');
        
        // Switch to edit form
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', function() {
                profileDisplay.style.display = 'none';
                profileEdit.style.display = 'block';
                // Scroll to the form
                window.scrollTo({
                    top: profileEdit.offsetTop - 20,
                    behavior: 'smooth'
                });
            });
        }
        
        // Switch back to profile view
        if (viewProfileBtn) {
            viewProfileBtn.addEventListener('click', function() {
                profileEdit.style.display = 'none';
                profileDisplay.style.display = 'block';
                // Scroll to the view
                window.scrollTo({
                    top: profileDisplay.offsetTop - 20,
                    behavior: 'smooth'
                });
            });
        }
        
        // Check URL parameter to show edit form directly if needed
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('edit') === 'true') {
            if (profileDisplay && profileEdit) {
                profileDisplay.style.display = 'none';
                profileEdit.style.display = 'block';
            }
        }
        
        // Image upload functionality
        const imageInput = document.getElementById('modalProfileImage');
        const imagePreview = document.getElementById('imagePreview');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const uploadForm = document.getElementById('profileImageForm');
        const uploadError = document.getElementById('uploadError');
        
        // Show image preview when a file is selected
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewContainer.style.display = 'none';
                }
            });
        }
        
        // Handle form submission
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const uploadBtn = document.getElementById('uploadBtn');
                uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
                uploadBtn.disabled = true;
                uploadError.style.display = 'none';
                
                fetch('upload_profile_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        uploadError.textContent = data.error;
                        uploadError.style.display = 'block';
                    } else if (data.success) {
                        // Refresh the page to show the updated profile image
                        window.location.reload();
                    }
                })
                .catch(error => {
                    uploadError.textContent = 'Error uploading image. Please try again.';
                    uploadError.style.display = 'block';
                    console.error('Error:', error);
                })
                .finally(() => {
                    uploadBtn.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Image';
                    uploadBtn.disabled = false;
                });
            });
        }
    });
    </script>
</body>
</html>
