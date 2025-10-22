<!-- Driver Selection Panel -->
  <!-- Overlay -->
  <div class="ds-overlay" id="dsOverlay"></div>

  <!-- Driver Selection Panel -->
  <div class="ds-panel" id="dsPanel">
    <form id="dsForm">
      <div class="ds-body">
        <div class="ds-title">Select Driver</div>

        <!-- Driver Selection -->
        <div class="ds-field">
          <label for="ds-driver">Choose Your Driver</label>
          <select id="ds-driver" name="driver">
            <option value="">Select a driver</option>
            <option value="driver1">John Smith - Professional Driver (4.8★)</option>
            <option value="driver2">Priya Fernando - Family Tours (4.9★)</option>
            <option value="driver3">Ravi Perera - Adventure Expert (4.7★)</option>
            <option value="driver4">Samantha W. - Cultural Guide (4.9★)</option>
          </select>
          <div class="ds-error"></div>
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
          <div class="ds-price-amount" id="dsPriceAmount">$0.00 / day</div>
        </div>
      </div>

      <!-- Footer -->
      <div class="ds-footer">
        <button type="button" class="ds-cancel" id="dsCancel">Cancel</button>
        <button type="submit" class="ds-add">ADD DRIVER</button>
      </div>
    </form>
  </div>
