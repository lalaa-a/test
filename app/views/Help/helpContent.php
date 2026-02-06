<!-- Help Interface -->
<link rel="stylesheet" href="<?php echo URL_ROOT.'/public/css/helper/help.css'?>">

<div class="help-container">
    <div class="help-header">
        <h1><i class="fas fa-headset"></i> Help Center</h1>
        <p>How can we help you today?</p>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="action-card" onclick="scrollToSection('faq-section')">
            <i class="fas fa-question-circle"></i>
            <h3>FAQs</h3>
            <p>Find answers to common questions</p>
        </div>
        <div class="action-card" onclick="scrollToSection('contact-section')">
            <i class="fas fa-envelope"></i>
            <h3>Contact Us</h3>
            <p>Get in touch with our team</p>
        </div>
        <div class="action-card" onclick="openHelpChat()">
            <i class="fas fa-comments"></i>
            <h3>Chat with Us</h3>
            <p>Chat with our support team</p>
        </div>
        <div class="action-card" onclick="scrollToSection('guide-section')">
            <i class="fas fa-book"></i>
            <h3>User Guide</h3>
            <p>Learn how to use the platform</p>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section" id="faq-section">
        <h2><i class="fas fa-question-circle"></i> Frequently Asked Questions</h2>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How do I book a trip?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>To book a trip, navigate to the Destinations or Trips section, select your preferred destination, choose your travel dates, and follow the booking process. You can also browse available drivers and guides to customize your travel experience.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How can I contact my driver or guide?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Once your booking is confirmed, you can message your driver or guide directly through the Messaging section in your dashboard. You'll also receive their contact information in your booking confirmation.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How do I cancel or modify my booking?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Go to your Trips section, find the booking you want to modify, and click on the edit or cancel option. Please note that cancellation policies may apply depending on how close you are to the travel date.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>What payment methods are accepted?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>We accept major credit cards, debit cards, and online bank transfers. All payments are processed securely through our payment gateway.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How do I update my profile information?</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Click on your profile picture or navigate to the Profile section in the sidebar. From there, you can update your personal information, contact details, and preferences.</p>
            </div>
        </div>
    </div>

    <!-- User Guide Section -->
    <div class="faq-section" id="guide-section">
        <h2><i class="fas fa-book"></i> User Guide</h2>
        
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Getting Started</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>Welcome to our platform! Start by exploring the Dashboard to see an overview of your account. Use the sidebar menu to navigate between different sections like Destinations, Trips, and more.</p>
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Navigating the Dashboard</span>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="faq-answer">
                <p>The sidebar on the left provides quick access to all features. Click on any menu item to load that section. Your profile and account settings are accessible from the user menu at the bottom of the sidebar.</p>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="contact-section" id="contact-section">
        <h2><i class="fas fa-envelope"></i> Contact Support</h2>
        
        <div class="contact-grid">
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>Email</h4>
                        <p>helpcenter@gmail.com</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h4>Phone</h4>
                        <p>+94 11 234 5678</p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h4>Support Hours</h4>
                        <p>Everyday: 7:00 AM - 10:00 PM</p>
                    </div>
                </div>
            </div>

            <form class="contact-form" id="helpContactForm">
                <div class="form-group">
                    <label for="helpSubject">Subject</label>
                    <select id="helpSubject" required>
                        <option value="">Select a topic</option>
                        <option value="booking">Booking Issues</option>
                        <option value="payment">Payment Problems</option>
                        <option value="account">Account Help</option>
                        <option value="complaint">File a Complaint</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="helpMessage">Message</label>
                    <textarea id="helpMessage" rows="4" placeholder="Describe your issue..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo URL_ROOT.'/public/js/helper/help.js'?>"></script>
