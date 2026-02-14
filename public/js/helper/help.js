// Premium Chat Widget Logic

// Open chat widget from Help Center page (uses the dashboard's existing chat widget)
function openHelpChat() {
    const chatWidget = document.getElementById('chatWidget');
    const helpPopup = document.getElementById('helpOptionsPopup');
    if (chatWidget) {
        // Close help popup if open
        if (helpPopup) helpPopup.classList.remove('active');
        chatWidget.classList.add('active');
        setTimeout(() => {
            const chatInput = document.getElementById('chatInput');
            if (chatInput) chatInput.focus();
        }, 300);
    }
}

function toggleChat() {
    const chatWidget = document.getElementById('chatWidget');
    chatWidget.classList.toggle('active');

    // Focus input when opening
    if (chatWidget.classList.contains('active')) {
        setTimeout(() => {
            document.getElementById('chatInput').focus();
        }, 300);
    }
}

// Chat simulation functions removed to prevent conflict with real chat widget (helpWidget.js)
function toggleFaq(element) {
    const faqItem = element.parentElement;
    const answer = faqItem.querySelector('.faq-answer');
    const isActive = element.classList.contains('active');

    // Close all other FAQs
    document.querySelectorAll('.faq-question').forEach(q => {
        q.classList.remove('active');
        q.parentElement.querySelector('.faq-answer').classList.remove('show');
    });

    // Toggle current FAQ
    if (!isActive) {
        element.classList.add('active');
        answer.classList.add('show');
    }
}

// Handle contact form submission
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('helpContactForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const subject = document.getElementById('helpSubject').value;
            const message = document.getElementById('helpMessage').value;

            if (!subject || !message) {
                showNotification('Please fill in all fields', 'warning');
                return;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;

            // Simulate form submission (replace with actual API call)
            setTimeout(() => {
                showNotification('Your message has been sent! We\'ll get back to you soon.', 'success');
                form.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 1500);
        });
    }
});

// Show notification (uses global notification function if available)
function showHelpNotification(message, type) {
    if (typeof showNotification === 'function') {
        showNotification(message, type);
    } else {
        alert(message);
    }
}

// Scroll to target section
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

        setTimeout(() => {
            section.style.backgroundColor = originalBg;
        }, 1000);
    }
}
