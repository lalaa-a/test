<!-- Navigation bars -->
    <div class="navigation-container">
        <!-- Upper navigation bar -->
        <div class="upper-nav">
            <div class="nav-content">
                <div class="logo">
                    <img src="<?php echo IMG_ROOT?>/logo/logo design 1(2).png" alt="Logo">
                </div>
                <div class="nav-buttons">
                    <button id="tripsButton" class="nav-btn" data-hover-color="#9ACBD0">Trips</button>
                    
                    <!--    <button class="nav-btn" data-hover-color="#9ACBD0">Help</button> -->
                    <button class="nav-btn" data-hover-color="#9ACBD0" onclick="window.location.href='<?php echo URL_ROOT; ?>/User/aboutUs'">About us</button>
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <?php 
                    $user = getLoggedInUser();
                    $profilePhoto = !empty($user['profile_photo']) ? URL_ROOT . '/public/' . $user['profile_photo'] : URL_ROOT . '/public/img/default-avatar.png';
                    ?>
                    <!-- Logged in user section -->
                    <div class="user-section">
                        <div class="user-info" onclick="toggleUserDropdown()">
                            <img src="<?= $profilePhoto ?>" alt="Profile" class="user-avatar">
                            <span class="user-name"><?= htmlspecialchars($user['fullname']) ?></span>
                            <span class="dropdown-arrow">▼</span>
                        </div>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="<?= URL_ROOT ?>/User/account" class="dropdown-item">
                                <span class="dropdown-icon">👤</span>
                                My Account
                            </a>
                            <?php if ($user['account_type'] === 'tourist'): ?>
                                <a href="<?= URL_ROOT ?>/User/trips" class="dropdown-item">
                                    <span class="dropdown-icon">🧳</span>
                                    My Trips
                                </a>
                            <?php elseif ($user['account_type'] === 'guide'): ?>
                                <a href="<?= URL_ROOT ?>/Guide" class="dropdown-item">
                                    <span class="dropdown-icon">🗺️</span>
                                    Guide Dashboard
                                </a>
                            <?php elseif ($user['account_type'] === 'driver'): ?>
                                <a href="<?= URL_ROOT ?>/Driver" class="dropdown-item">
                                    <span class="dropdown-icon">🚗</span>
                                    Driver Dashboard
                                </a>
                            <?php elseif ($user['account_type'] === 'admin'): ?>
                                <a href="<?= URL_ROOT ?>/Admin" class="dropdown-item">
                                    <span class="dropdown-icon">⚙️</span>
                                    Admin Dashboard
                                </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= URL_ROOT ?>/User/logout" class="dropdown-item logout-item">
                                <span class="dropdown-icon">🚪</span>
                                Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Not logged in section -->
                    <div class="signin-btn">
                        <a href="/test/User/register" class="register-link">Register</a>
                        <button class="signin-button" onclick="window.location.href='/test/User/login'">Sign In</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Second navigation bar -->
        <div class="second-nav">
            <div>
                <nav class="nav-links">
                    <a href="/test/Home" class="nav-link" data-underline-color="#9ACBD0">Home</a>
                    <a href="<?= URL_ROOT ?>/User/allDestinations" class="nav-link" data-underline-color="#9ACBD0">Destinations</a>
                    <a href="/test/DriverController" class="nav-link" data-underline-color="#9ACBD0">Drivers</a>
                    <a href="/test/GuideController" class="nav-link" data-underline-color="#9ACBD0">Guides</a>
                    <a href="<?= URL_ROOT ?>/User/packages" class="nav-link" data-underline-color="#9ACBD0">Packages</a>
                    <a href="<?= URL_ROOT ?>/User/trending" class="nav-link" data-underline-color="#9ACBD0">Trending List</a>
                </nav>
            </div>
        </div>
    </div>



