<!-- Driver Selection Panel -->
  <!-- Overlay -->
  <div class="ds-overlay" id="dsOverlay"></div>

  <!-- Driver Selection Panel -->
  <div class="ds-panel" id="dsPanel">
    <form id="dsForm">
      <div class="ds-body">
        <div class="ds-title">Select Driver</div>

        <!-- Driver Search -->
        <div class="ds-field">
          <label for="ds-driver-search">Search Drivers</label>
          <div class="ds-search-container">
            <input type="text" id="ds-driver-search" name="driver-search" placeholder="Search by name, location, or specialty..." autocomplete="off">
            <div class="ds-search-icon">🔍</div>
          </div>
        </div>

        <!-- Driver Selection -->
        <div class="ds-field">
          <label for="ds-driver">Choose Your Driver</label>
          <select id="ds-driver" name="driver">
            <option value="">Select a driver</option>
            <option value="driver1" data-name="Pasan Mihiranga" data-location="Colombo" data-specialty="City Tours">Pasan Mihiranga - Colombo</option>
            <option value="driver2" data-name="Vihanga Fernando" data-location="Kandy" data-specialty="Cultural Sites">Vihanga Fernando </option>
            <option value="driver3" data-name="Ravi Perera" data-location="Galle" data-specialty="Coastal Tours">Ravi Perera</option>
            <option value="driver4" data-name="Saman Kumara" data-location="Nuwara Eliya" data-specialty="Hill Country">Saman Kumara</option>
            <option value="driver5" data-name="Chamara Silva" data-location="Sigiriya" data-specialty="Ancient Sites">Chamara Silva</option>
            <option value="driver6" data-name="Nuwan Jayasinghe" data-location="Ella" data-specialty="Adventure Tours">Nuwan Jayasinghe</option>
            <option value="driver7" data-name="Tharaka Bandara" data-location="Anuradhapura" data-specialty="Heritage Tours">Tharaka Bandara</option>
            <option value="driver8" data-name="Kamal Wickramasinghe" data-location="Negombo" data-specialty="Airport Transfers">Kamal Wickramasinghe</option>
          </select>
          <div class="ds-error"></div>
          <div class="ds-search-results" id="dsSearchResults"></div>
        </div>


        <!-- Vehicle Type -->
        <div class="ds-field">
          <label for="ds-vehicle">Vehicle Type</label>
          <select id="ds-vehicle" name="vehicle">
            <option value="">Select vehicle type</option>
            <option value="sedan">Sedan</option>
            <option value="suv">SUV</option>
            <option value="van">Van</option>
            <option value="luxury">Luxury Car</option>
            <option value="minibus">Mini Bus</option>
          </select>
          <div class="ds-error"></div>
        </div>

        <!-- Special Requirements -->
        <div class="ds-field">
          <label for="ds-requirements">Special Requirements</label>
          <textarea id="ds-requirements" name="requirements" placeholder="Air conditioning, child seats, wheelchair accessible, etc."></textarea>
          <div class="ds-error"></div>
        </div>

        <!-- Price Display -->
        <div class="ds-price-display">
          <div class="ds-price-label">Estimated Cost:</div>
          <div class="ds-price-amount" id="dsPriceAmount">Rs 0.00 / day</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="ds-footer">
        <button type="button" class="ds-cancel" id="dsCancel">Cancel</button>
        <button type="submit" class="ds-add">ADD DRIVER</button>
      </div>
    </form>
  </div>
