<?php
/**
 * Functions to handle session management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set session variable
 * @param string $key
 * @param mixed $value
 */
function setSession($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Get session variable
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function getSession($key, $default = null) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

/**
 * Check if session variable exists
 * @param string $key
 * @return bool
 */
function hasSession($key) {
    return isset($_SESSION[$key]);
}

/**
 * Unset session variable
 * @param string $key
 */
function unsetSession($key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

/**
 * Destroy all sessions
 */
function destroySession() {
    session_destroy();
    $_SESSION = [];
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return hasSession('user_id') && hasSession('user_email');
}

/**
 * Get logged in user data
 * @return array|null
 */
function getLoggedInUser() {
    if (isLoggedIn()) {
        return [
            'id' => getSession('user_id'),
            'email' => getSession('user_email'),
            'fullname' => getSession('user_fullname'),
            'account_type' => getSession('user_account_type'),
            'profile_photo' => getSession('user_profile_photo')
        ];
    }
    return null;
}

/**
 * Set user session data
 * @param array $user
 */

function setUserSession($user) {
    setSession('user_id', $user['id']);
    setSession('user_email', $user['email']);
    setSession('user_fullname', $user['fullname']);
    setSession('user_account_type', $user['account_type']);
    setSession('user_profile_photo', $user['profile_photo']);
    setSession('user_logged_in', true);
    setSession('user_login_time', time());
}

/**
 * Clear user session data
 */
function clearUserSession() {

    $sessionKeys = [
        'user_id', 'user_email', 'user_fullname', 
        'user_account_type', 'user_profile_photo', 
        'user_logged_in', 'user_login_time'
    ];
    
    foreach ($sessionKeys as $key) {
        unsetSession($key);
    }
}

/**
 * Redirect if not logged in
 * @param string $redirect_url
 */
function requireLogin($redirect_url = '/user/login') {
    if (!isLoggedIn()) {
        header('Location: ' . URL_ROOT . $redirect_url);
        exit();
    }
}

/**
 * Redirect if already logged in
 * @param string $redirect_url
 */
function redirectIfLoggedIn($redirect_url = '/dashboard') {
    if (isLoggedIn()) {
        $accountType = getSession('user_account_type');
        switch ($accountType) {
            case 'admin':
                $redirect_url = '/dashboard/admin';
                break;
            case 'site_modertor':
                $redirect_url = '/dashboard/siteModerator';
                break;
            case 'business_manager':
                $redirect_url = '/dashboard/businessManager';
                break;
            case 'driver':
                $redirect_url = '/dashboard/driver';
                break;
            case 'guide':
                $redirect_url = '/dashboard/guide';
                break;
            case 'tourist':
                $redirect_url = '/RegUser/home';
                break;
        }
        header('Location: ' . URL_ROOT . $redirect_url);
        exit();
    }
}

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlash($type, $message) {
    setSession('flash_message', [
        'type' => $type,
        'message' => $message
    ]);
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlash() {
    $flash = getSession('flash_message');
    unsetSession('flash_message');
    return $flash;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!hasSession('csrf_token')) {
        setSession('csrf_token', bin2hex(random_bytes(32)));
    }
    return getSession('csrf_token');
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return hasSession('csrf_token') && hash_equals(getSession('csrf_token'), $token);
}

/**
 * Regenerate session ID for security
 */
function regenerateSession() {
    session_regenerate_id(true);
}

/**
 * Set remember me cookie
 * @param array $user
 * @param int $duration (in seconds, default 30 days)
 */

function setRememberMeCookie($user, $duration = 2592000) {

    // Ensure UserModel is available
    if (!class_exists('UserModel')) {
        require_once '../app/models/UserModel.php';
    }
    
    $user_model = new UserModel();

    $token = bin2hex(random_bytes(32));
    $tokenHash = password_hash($token, PASSWORD_DEFAULT);
    $expiry = date('Y-m-d H:i:s', time() + $duration);
    
    //$user_model->updateLastLogin($user['id'],$tokenHash,$expiry);
    $user_model->updateRememberToken($user['id'],$tokenHash,$expiry);

    setcookie('remember_token', $token, time() + $duration, '/', '', false, true);
    setSession('remember_token', $token);
}


/**
 * Clear remember me cookie
 */
function clearRememberMeCookie() {
    
    if(isset($_COOKIE['remember_token'])){
        $token = $_COOKIE['remember_token'];
        
        // Ensure UserModel is available
        if (!class_exists('UserModel')) {
            require_once '../app/models/UserModel.php';
        }
        
        $user_model = new UserModel();
        $user_model->deleteRememberToken($token);
    }

    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    unsetSession('remember_token');
}



/**
 * Check if remember me is active
 * @return bool
 */
function hasRememberMe() {
    /*
    return isset($_COOKIE['remember_token']) && hasSession('remember_token') && 
           $_COOKIE['remember_token'] === getSession('remember_token');
    */

    if (!isset($_COOKIE['remember_token']) || !hasSession('remember_token') || 
        $_COOKIE['remember_token'] !== getSession('remember_token')) {
        return false;
    }

    // Ensure UserModel is available
    if (!class_exists('UserModel')) {
        require_once '../app/models/UserModel.php';
    }

    $user_model = new UserModel();
    $userId = $user_model->findByRememberToken($_COOKIE['remember_token']);

    if ($userId) {
        // Auto-login: Fetch user and set session
        $userModel = new UserModel();
        $user = $userModel->findById($userId);
        if ($user) {
            setUserSession([
                'id' => $user->id,
                'email' => $user->email,
                'fullname' => $user->fullname,
                'account_type' => $user->account_type,
                'profile_photo' => $user->profile_photo
            ]);
            return true;
        }
    }
    return false;
}
?>