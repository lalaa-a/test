<?php

if (!isset($helpRoute)) {
    $helpRoute = 'helpc';
}
?>

<!-- Help Widget CSS -->
<link rel="stylesheet" href="<?php echo URL_ROOT . '/public/css/helper/helpWidget.css' ?>">

<!-- Help Widget Container -->
<div class="help-widget-container">
    <!-- Help Options Popup -->
    <div class="help-options-popup" id="helpOptionsPopup">
        <div class="help-popup-header">
            <h4><i class="fas fa-hands-helping"></i> How can we help?</h4>
        </div>
        <div class="help-option-item" id="openChatBtn">
            <div class="help-option-icon chat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="help-option-text">
                <h5>Chat with Us</h5>
                <p>Chat with our support team</p>
            </div>
        </div>
        <a href="<?php echo URL_ROOT . '/' . $helpRoute ?>" class="help-option-item">
            <div class="help-option-icon center-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="help-option-text">
                <h5>Help Center</h5>
                <p>Browse FAQs & guides</p>
            </div>
        </a>
    </div>

    <!-- Floating Help Button -->
    <button class="floating-help-btn" id="helpBtn" title="Need Help?">
        <img src="<?php echo IMG_ROOT . '/help/support.png' ?>" alt="Help">
    </button>
</div>