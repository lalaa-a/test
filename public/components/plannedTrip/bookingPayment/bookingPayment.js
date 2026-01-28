(function(){
      const openBtn = document.getElementById("openBookingPayment");
      const panel = document.getElementById('bpPanel');
      const overlay = document.getElementById('bpOverlay');
      const cancelBtn = document.getElementById('bpCancel');
      const form = document.getElementById('bpForm');
      const paymentMethodSelect = document.getElementById('bp-payment-method');
      const cardDetails = document.getElementById('bpCardDetails');
  const cardNumberInput = document.getElementById('bp-card-number');
  const expiryInput = document.getElementById('bp-expiry');
  const cvvInput = document.getElementById('bp-cvv');
  const cardholderInput = document.getElementById('bp-cardholder');
  const phoneInput = document.getElementById('bp-phone');
  const billingAddressInput = document.getElementById('bp-billing-address');
  const bpBody = panel ? panel.querySelector('.bp-body') : null;

      function openPanel(){
        panel.classList.add('active');
        overlay.classList.add('active');
      }

      function closePanel(){
        panel.classList.remove('active');
        overlay.classList.remove('active');
      }

      function toggleCardDetails(){
        const selectedMethod = paymentMethodSelect.value;
        if (selectedMethod === 'credit-card' || selectedMethod === 'debit-card') {
          cardDetails.style.display = 'block';
          // ensure card section is visible on small screens
          scrollToCardDetails();
        } else {
          cardDetails.style.display = 'none';
        }
      }

      // Format card number input
      function formatCardNumber(value) {
        const digits = value.replace(/\D/g, '');
        return digits.replace(/(\d{4})(?=\d)/g, '$1 ');
      }

      // Format expiry date
      function formatExpiry(value) {
        const digits = value.replace(/\D/g, '');
        if (digits.length >= 2) {
          return digits.substring(0, 2) + '/' + digits.substring(2, 4);
        }
        return digits;
      }

      // Helpers to ensure focused inputs are visible on small screens
      function scrollIntoViewFor(el) {
        if (!el) return;
        // small delay to allow virtual keyboard to open
        setTimeout(() => {
          try {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
          } catch (e) {
            el.scrollIntoView();
          }
        }, 300);
      }

      function scrollToCardDetails() {
        if (!cardDetails) return;
        setTimeout(() => {
          try {
            cardDetails.scrollIntoView({ behavior: 'smooth', block: 'center' });
          } catch (e) {
            cardDetails.scrollIntoView();
          }
        }, 300);
      }

      // Event listeners
      if (openBtn) {
        openBtn.addEventListener('click', openPanel);
      }

      cancelBtn.addEventListener('click', closePanel);
      overlay.addEventListener('click', closePanel);
      paymentMethodSelect.addEventListener('change', toggleCardDetails);

      // Card number formatting
      cardNumberInput.addEventListener('input', function(e) {
        const formatted = formatCardNumber(e.target.value);
        e.target.value = formatted;
      });

      // Ensure focused inputs are visible (useful on mobile when keyboard opens)
      [cardNumberInput, expiryInput, cvvInput, cardholderInput, phoneInput, billingAddressInput].forEach(input => {
        if (!input) return;
        input.addEventListener('focus', function() {
          scrollIntoViewFor(input);
        });
      });

      // Expiry date formatting
      expiryInput.addEventListener('input', function(e) {
        const formatted = formatExpiry(e.target.value);
        e.target.value = formatted;
      });

      // CVV validation (numbers only)
      cvvInput.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
      });

      // Form validation
      form.addEventListener('submit', function(e){
        e.preventDefault();
        let valid = true;

        // Clear old errors
        form.querySelectorAll('.bp-error').forEach(el => el.textContent = '');

        const paymentMethod = document.getElementById('bp-payment-method');
        const phone = document.getElementById('bp-phone');
        const billingAddress = document.getElementById('bp-billing-address');
        const terms = document.getElementById('bp-terms');

        // Basic validations
        if (!paymentMethod.value) {
          paymentMethod.nextElementSibling.textContent = 'Please select a payment method';
          valid = false;
        }

        if (!phone.value.trim()) {
          phone.nextElementSibling.textContent = 'Please enter contact phone';
          valid = false;
        }

        if (!billingAddress.value.trim()) {
          billingAddress.nextElementSibling.textContent = 'Please enter billing address';
          valid = false;
        }

        if (!terms.checked) {
          terms.parentElement.querySelector('.bp-error').textContent = 'Please accept terms and conditions';
          valid = false;
        }

        // Card validation (if card payment selected)
        if (paymentMethod.value === 'credit-card' || paymentMethod.value === 'debit-card') {
          const cardNumber = document.getElementById('bp-card-number');
          const expiry = document.getElementById('bp-expiry');
          const cvv = document.getElementById('bp-cvv');
          const cardholder = document.getElementById('bp-cardholder');

          if (!cardNumber.value.trim()) {
            cardNumber.nextElementSibling.textContent = 'Please enter card number';
            valid = false;
          }

          if (!expiry.value.trim()) {
            expiry.nextElementSibling.textContent = 'Please enter expiry date';
            valid = false;
          } else if (!/^\d{2}\/\d{2}$/.test(expiry.value)) {
            expiry.nextElementSibling.textContent = 'Please enter valid expiry date (MM/YY)';
            valid = false;
          }

          if (!cvv.value.trim()) {
            cvv.nextElementSibling.textContent = 'Please enter CVV';
            valid = false;
          }

          if (!cardholder.value.trim()) {
            cardholder.nextElementSibling.textContent = 'Please enter cardholder name';
            valid = false;
          }
        }

        if (valid) {
          // Simulate payment processing
          const payBtn = form.querySelector('.bp-pay');
          payBtn.disabled = true;
          payBtn.textContent = 'Processing...';

          setTimeout(() => {
            alert('Payment successful! Your booking has been confirmed. You will receive a confirmation email shortly.');
            closePanel();
            form.reset();
            cardDetails.style.display = 'none';
            payBtn.disabled = false;
            payBtn.textContent = 'PAY NOW';
          }, 2000);
        }
      });

      // Initialize card details visibility
      toggleCardDetails();
    })();
