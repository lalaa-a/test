(function(){
      const openBtn = document.getElementById("openDriverSelection");
      const panel = document.getElementById('dsPanel');
      const overlay = document.getElementById('dsOverlay');
      const cancelBtn = document.getElementById('dsCancel');
      const form = document.getElementById('dsForm');
      const driverSelect = document.getElementById('ds-driver');
      const priceAmount = document.getElementById('dsPriceAmount');

      // Driver pricing data
      const driverPrices = {
        'driver1': 75.00, // John Smith
        'driver2': 70.00, // Priya Fernando
        'driver3': 85.00, // Ravi Perera
        'driver4': 80.00  // Samantha Wijesinghe
      };

      function openPanel(){
        panel.classList.add('active');
        overlay.classList.add('active');
      }

      function closePanel(){
        panel.classList.remove('active');
        overlay.classList.remove('active');
      }

      function updatePrice(){
        const selectedDriver = driverSelect.value;
        if (selectedDriver && driverPrices[selectedDriver]) {
          priceAmount.textContent = `$${driverPrices[selectedDriver].toFixed(2)} / day`;
        } else {
          priceAmount.textContent = '$0.00 / day';
        }
      }

      // Event listeners
      if (openBtn) {
        openBtn.addEventListener('click', openPanel);
      }

      cancelBtn.addEventListener('click', closePanel);
      overlay.addEventListener('click', closePanel);
      driverSelect.addEventListener('change', updatePrice);

      // Form validation
      form.addEventListener('submit', function(e){
        e.preventDefault();
        let valid = true;

        // Clear old errors
        form.querySelectorAll('.ds-error').forEach(el => el.textContent = '');

        const driver = document.getElementById('ds-driver');
        const vehicle = document.getElementById('ds-vehicle');

        if (!driver.value) {
          driver.nextElementSibling.textContent = 'Please select a driver';
          valid = false;
        }
        if (!vehicle.value) {
          vehicle.nextElementSibling.textContent = 'Please select vehicle type';
          valid = false;
        }

        if (valid) {
          // Get selected driver info
          const selectedDriverText = driver.options[driver.selectedIndex].text;
          const dailyRate = driverPrices[driver.value] || 0;

          alert(`Driver "${selectedDriverText}" added successfully! Rate: $${dailyRate.toFixed(2)}/day. You can now proceed to payment.`);
          closePanel();
          
          // Show the pay button after driver is added
          const payButton = document.getElementById('openBookingPayment');
          if (payButton) {
            payButton.style.display = 'flex';
            payButton.style.opacity = '1';
            payButton.style.animation = 'fadeInScale 0.5s ease-out';
          }
          
          form.reset();
          updatePrice();
        }
      });

      // Initialize price display
      updatePrice();
    })();