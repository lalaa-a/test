<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <title>My Account - TripinGoo</title>

    <style>
        body {
            font-family: 'Geologica';
            background: #fff;
            color: #111;
            margin: 0;
            padding: 0;
        }

        .account-container {
            max-width: 800px;
            margin: 80px auto 40px;
            padding: 0 20px;
        }

        .account-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .account-header h1 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .account-header p {
            color: #7f8c8d;
            font-size: 1.1rem;
        }

        .account-content {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .profile-section {
            background: linear-gradient(135deg, #006A71 0%, #48A6A7 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 20px;
            object-fit: cover;
            display: block;
        }

        .profile-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .profile-type {
            font-size: 1rem;
            opacity: 0.9;
            text-transform: capitalize;
        }

        .form-section {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Geologica';
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            background: #006A71;
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            font-family: 'Geologica';
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #6c757d;
            margin-left: 10px;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 1000;
            transition: opacity 0.3s ease;
        }

        .notification.success {
            background: #28a745;
        }

        .notification.error {
            background: #dc3545;
        }

        .photo-upload {
            margin-top: 20px;
        }

        .photo-upload input[type="file"] {
            display: none;
        }

        .photo-upload-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px dashed rgba(255, 255, 255, 0.5);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .photo-upload-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.8);
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .account-container {
                margin: 60px auto 20px;
                padding: 0 15px;
            }

            .profile-section {
                padding: 30px 20px;
            }

            .form-section {
                padding: 30px 20px;
            }

            .account-header h1 {
                font-size: 2rem;
            }
        }
    </style>

    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        printAssets();
    ?>

</head>
<body>
    
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>

    <div class="account-container">
        <div class="account-header">
            <h1>My Account</h1>
            <p>Manage your profile information and preferences</p>
        </div>

        <div class="account-content">
            <div class="profile-section">
                <?php
                $user = getLoggedInUser();
                $profilePhoto = !empty($user['profile_photo']) ? URL_ROOT.'/public/'.$user['profile_photo'] : URL_ROOT . '/public/img/default-avatar.png';
                ?>
                <img src="<?= $profilePhoto ?>" alt="Profile Photo" class="profile-photo" id="profilePhotoDisplay">
                <div class="profile-name"><?= htmlspecialchars($user['fullname']) ?></div>
                <div class="profile-type"><?= htmlspecialchars($user['account_type']) ?></div>
                
                <div class="photo-upload">
                    <label for="profilePhotoInput" class="photo-upload-btn">
                        📷 Change Photo
                    </label>
                    <input type="file" id="profilePhotoInput" accept="image/*">
                </div>
            </div>

            <div class="form-section">
                <form id="updateAccountForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullname">Full Name *</label>
                            <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($data['user']->fullname ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['user']->email ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($data['user']->phone ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="secondary_phone">Secondary Phone</label>
                            <input type="tel" id="secondary_phone" name="secondary_phone" value="<?= htmlspecialchars($data['user']->secondary_phone ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="language">Primary Language *</label>
                            <select id="language" name="language" required>
                                <option value="">Select Language</option>
                                <option value="English" <?= ($data['user']->language ?? '') === 'English' ? 'selected' : '' ?>>English</option>
                                <option value="Spanish" <?= ($data['user']->language ?? '') === 'Spanish' ? 'selected' : '' ?>>Spanish</option>
                                <option value="French" <?= ($data['user']->language ?? '') === 'French' ? 'selected' : '' ?>>French</option>
                                <option value="German" <?= ($data['user']->language ?? '') === 'German' ? 'selected' : '' ?>>German</option>
                                <option value="Italian" <?= ($data['user']->language ?? '') === 'Italian' ? 'selected' : '' ?>>Italian</option>
                                <option value="Portuguese" <?= ($data['user']->language ?? '') === 'Portuguese' ? 'selected' : '' ?>>Portuguese</option>
                                <option value="Chinese" <?= ($data['user']->language ?? '') === 'Chinese' ? 'selected' : '' ?>>Chinese</option>
                                <option value="Japanese" <?= ($data['user']->language ?? '') === 'Japanese' ? 'selected' : '' ?>>Japanese</option>
                                <option value="Arabic" <?= ($data['user']->language ?? '') === 'Arabic' ? 'selected' : '' ?>>Arabic</option>
                                <option value="Hindi" <?= ($data['user']->language ?? '') === 'Hindi' ? 'selected' : '' ?>>Hindi</option>
                                <option value="Sinhala" <?= ($data['user']->language ?? '') === 'Sinhala' ? 'selected' : '' ?>>Sinhala</option>
                                <option value="Tamil" <?= ($data['user']->language ?? '') === 'Tamil' ? 'selected' : '' ?>>Tamil</option>
                                <option value="Other" <?= ($data['user']->language ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?= ($data['user']->gender ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= ($data['user']->gender ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= ($data['user']->gender ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                <option value="Prefer not to say" <?= ($data['user']->gender ?? '') === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth *</label>
                        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($data['user']->dob ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" required><?= htmlspecialchars($data['user']->address ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn" id="updateBtn">Update Information</button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php renderComponent('inc','footer',[]); ?>

    <script>
        // Show notification function
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Handle profile photo upload
        document.getElementById('profilePhotoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                showNotification('Please select a valid image file (JPG, JPEG, or PNG)', 'error');
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showNotification('Image file size must be less than 5MB', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('profile_photo', file);

            fetch('<?= URL_ROOT ?>/User/updateProfilePhoto', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('profilePhotoDisplay').src = data.new_photo_url;
                    showNotification('Profile photo updated successfully!');
                } else {
                    showNotification(data.message || 'Failed to update profile photo', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the photo', 'error');
            });
        });

        // Handle form submission
        document.getElementById('updateAccountForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const updateBtn = document.getElementById('updateBtn');
            updateBtn.disabled = true;
            updateBtn.textContent = 'Updating...';

            const formData = new FormData(this);
            const jsonData = Object.fromEntries(formData);

            fetch('<?= URL_ROOT ?>/User/updateAccount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Account information updated successfully!');
                    // Update the profile name in the header
                    document.querySelector('.profile-name').textContent = jsonData.fullname;
                } else {
                    showNotification(data.message || 'Failed to update account information', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating your information', 'error');
            })
            .finally(() => {
                updateBtn.disabled = false;
                updateBtn.textContent = 'Update Information';
            });
        });

        // Reset form function
        function resetForm() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.getElementById('updateAccountForm').reset();
                // You might want to reload the page to get fresh data
                location.reload();
            }
        }
    </script>

</body>
</html>