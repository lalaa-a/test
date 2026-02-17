<!-- Example page content -->
  

  <!-- Overlay -->
  <div class="al-overlay" id="alOverlay"></div>

  <!-- Add Locations Panel -->
  <div class="al-panel" id="alPanel">
    <form id="alForm">
      <div class="al-body">
        <div class="al-title">Add Locations</div>

        <!-- Location Type -->
        <div class="al-field">
          <label for="al-type">Location Type</label>
          <select id="al-type" name="type">
            <option value="">Select Location Type</option>
            <option value="checking">Checking</option>
            <option value="plain">Plain Location</option>
            <option value="checkout">Checkout</option>
          </select>
          <div class="al-error"></div>
        </div>

        <!-- Start + End Time -->
        <div class="al-row">
          <div class="al-field">
            <label for="al-start">Start time</label>
            <input type="time" id="al-start" name="start">
            <div class="al-error"></div>
          </div>
          <div class="al-field">
            <label for="al-end">End time</label>
            <input type="time" id="al-end" name="end">
            <div class="al-error"></div>
          </div>
        </div>

        <!-- Notes -->
        <div class="al-field">
          <label for="al-notes">Notes</label>
          <textarea id="al-notes" name="notes"></textarea>
          <div class="al-error"></div>
        </div>

        <!-- Add location -->
        <div class="al-field">
          <label for="al-location">Add location</label>
          <input type="text" id="al-location" name="location" placeholder="Search From the map and Add">
          <div class="al-error"></div>
        </div>
      </div>

      <!-- Footer -->
      <div class="al-footer">
        <button type="button" class="al-cancel" id="alCancel">Cancel</button>
        <button type="submit" class="al-add">ADD</button>
      </div>
    </form>
  </div>
