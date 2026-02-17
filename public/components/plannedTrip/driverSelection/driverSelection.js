(function(){
      const openBtn = document.getElementById("openDriverSelection");
      const panel = document.getElementById('dsPanel');
      const overlay = document.getElementById('dsOverlay');
      const cancelBtn = document.getElementById('dsCancel');
      const form = document.getElementById('dsForm');
      const driverSelect = document.getElementById('ds-driver');
      const priceAmount = document.getElementById('dsPriceAmount');
      const searchInput = document.getElementById('ds-driver-search');
      const searchResults = document.getElementById('dsSearchResults');

      // Driver pricing data
      const driverPrices = {
        'driver1': 11250.00, // Pasan Mihiranga (75 USD * 150)
        'driver2': 10500.00, // Vihanga Fernando (70 USD * 150)
        'driver3': 12750.00, // Ravi Perera (85 USD * 150)
        'driver4': 12000.00, // Saman Kumara (80 USD * 150)
        'driver5': 13500.00, // Chamara Silva (90 USD * 150)
        'driver6': 11625.00, // Nuwan Jayasinghe (77.5 USD * 150)
        'driver7': 10875.00, // Tharaka Bandara (72.5 USD * 150)
        'driver8': 9750.00   // Kamal Wickramasinghe (65 USD * 150)
      };

      // Driver data for search
      const driversData = [
        { value: 'driver1', name: 'Pasan Mihiranga', location: 'Colombo', specialty: 'City Tours' },
        { value: 'driver2', name: 'Vihanga Fernando', location: 'Kandy', specialty: 'Cultural Sites' },
        { value: 'driver3', name: 'Ravi Perera', location: 'Galle', specialty: 'Coastal Tours' },
        { value: 'driver4', name: 'Saman Kumara', location: 'Nuwara Eliya', specialty: 'Hill Country' },
        { value: 'driver5', name: 'Chamara Silva', location: 'Sigiriya', specialty: 'Ancient Sites' },
        { value: 'driver6', name: 'Nuwan Jayasinghe', location: 'Ella', specialty: 'Adventure Tours' },
        { value: 'driver7', name: 'Tharaka Bandara', location: 'Anuradhapura', specialty: 'Heritage Tours' },
        { value: 'driver8', name: 'Kamal Wickramasinghe', location: 'Negombo', specialty: 'Airport Transfers' }
      ];

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
          priceAmount.textContent = `Rs. ${driverPrices[selectedDriver].toFixed(2)} / day`;
        } else {
          priceAmount.textContent = 'Rs. 0.00 / day';
        }
      }

      // Search functionality
      function performSearch(searchTerm) {
        const filteredDrivers = driversData.filter(driver => {
          const searchLower = searchTerm.toLowerCase();
          return driver.name.toLowerCase().includes(searchLower) ||
                 driver.location.toLowerCase().includes(searchLower) ||
                 driver.specialty.toLowerCase().includes(searchLower);
        });

        displaySearchResults(filteredDrivers);
      }

      function displaySearchResults(drivers) {
        searchResults.innerHTML = '';

        if (drivers.length === 0) {
          searchResults.innerHTML = '<div class="ds-no-results">No drivers found matching your search</div>';
          searchResults.classList.add('active');
          return;
        }

        drivers.forEach(driver => {
          const resultItem = document.createElement('div');
          resultItem.className = 'ds-search-result-item';
          resultItem.dataset.value = driver.value;
          
          const price = driverPrices[driver.value] ? `Rs. ${driverPrices[driver.value].toFixed(2)}/day` : 'Price on request';
          
          resultItem.innerHTML = `
            <div class="ds-result-name">${driver.name}</div>
            <div class="ds-result-details">
              <span class="ds-result-location">${driver.location}</span>
              <span class="ds-result-specialty">${driver.specialty}</span>
              <div style="margin-top: 4px; font-weight: bold; color: #006A71;">${price}</div>
            </div>
          `;

          resultItem.addEventListener('click', () => selectDriverFromSearch(driver));
          searchResults.appendChild(resultItem);
        });

        searchResults.classList.add('active');
      }

      function selectDriverFromSearch(driver) {
        driverSelect.value = driver.value;
        searchInput.value = driver.name;
        searchResults.classList.remove('active');
        updatePrice();
        
        // Update selected item styling
        searchResults.querySelectorAll('.ds-search-result-item').forEach(item => {
          item.classList.remove('selected');
        });
        const selectedItem = searchResults.querySelector(`[data-value="${driver.value}"]`);
        if (selectedItem) {
          selectedItem.classList.add('selected');
        }
      }

      function hideSearchResults() {
        setTimeout(() => {
          searchResults.classList.remove('active');
        }, 200);
      }

      // Event listeners
      if (openBtn) {
        openBtn.addEventListener('click', openPanel);
      }

      cancelBtn.addEventListener('click', closePanel);
      overlay.addEventListener('click', closePanel);
      driverSelect.addEventListener('change', updatePrice);

      // Search event listeners
      searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();
        if (searchTerm.length >= 2) {
          performSearch(searchTerm);
        } else if (searchTerm.length === 0) {
          searchResults.classList.remove('active');
          driverSelect.value = '';
          updatePrice();
        }
      });

      searchInput.addEventListener('focus', function() {
        const searchTerm = this.value.trim();
        if (searchTerm.length >= 2) {
          performSearch(searchTerm);
        }
      });

      searchInput.addEventListener('blur', hideSearchResults);

      // Clear search when dropdown is changed directly
      driverSelect.addEventListener('change', function() {
        if (this.value) {
          const selectedDriver = driversData.find(driver => driver.value === this.value);
          if (selectedDriver) {
            searchInput.value = selectedDriver.name;
          }
        } else {
          searchInput.value = '';
        }
        updatePrice();
      });

      // Handle keyboard navigation in search results
      searchInput.addEventListener('keydown', function(e) {
        const items = searchResults.querySelectorAll('.ds-search-result-item');
        if (items.length === 0) return;

        let currentSelected = searchResults.querySelector('.ds-search-result-item.selected');
        let newSelected;

        switch(e.key) {
          case 'ArrowDown':
            e.preventDefault();
            if (currentSelected) {
              newSelected = currentSelected.nextElementSibling || items[0];
            } else {
              newSelected = items[0];
            }
            break;
          case 'ArrowUp':
            e.preventDefault();
            if (currentSelected) {
              newSelected = currentSelected.previousElementSibling || items[items.length - 1];
            } else {
              newSelected = items[items.length - 1];
            }
            break;
          case 'Enter':
            e.preventDefault();
            if (currentSelected) {
              currentSelected.click();
            }
            return;
          case 'Escape':
            searchResults.classList.remove('active');
            return;
        }

        if (newSelected) {
          items.forEach(item => item.classList.remove('selected'));
          newSelected.classList.add('selected');
        }
      });

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

          alert(`Driver "${selectedDriverText}" added successfully! Rate: Rs. ${dailyRate.toFixed(2)}/day. You can now proceed to payment.`);
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