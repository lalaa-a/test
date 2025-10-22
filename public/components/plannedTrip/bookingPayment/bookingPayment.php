<!-- Booking Payment Panel -->


<!-- Overlay -->
<div class="bp-overlay" id="bpOverlay" aria-hidden="true"></div>

<!-- Payment Panel -->
<aside class="bp-panel" id="bpPanel" role="dialog" aria-modal="true" aria-labelledby="bpTitle" aria-hidden="true">
  <form id="bpForm" novalidate>
    <!-- Make .bp-body the scrollable region -->
    <div class="bp-body" tabindex="-1">
      <div class="bp-title" id="bpTitle">Complete Your Booking</div>

      <!-- Booking Summary -->
      <section class="bp-summary" aria-label="Booking summary">
        <h3 class="sr-only">Booking Summary</h3>
        <div class="bp-summary-item">
          <span>Trip Duration:</span>
          <span id="bpDuration">3 days</span>
        </div>
        <div class="bp-summary-item">
          <span>Driver Service:</span>
          <span id="bpDriverCost">$225.00</span>
        </div>
        <div class="bp-summary-item">
          <span>Platform Fee:</span>
          <span>$15.00</span>
        </div>
        <div class="bp-summary-item total">
          <span>Total Amount:</span>
          <span id="bpTotalAmount">$240.00</span>
        </div>
      </section>

      <!-- Payment Method -->
      <div class="bp-field">
        <label for="bp-payment-method">Payment Method</label>
        <select id="bp-payment-method" name="payment_method" aria-describedby="bpPaymentMethodError">
          <option value="">Select payment method</option>
          <option value="credit-card">Credit Card</option>
          <option value="debit-card">Debit Card</option>
        </select>
        <div id="bpPaymentMethodError" class="bp-error" aria-live="polite"></div>
      </div>

      <!-- Credit Card Details -->
      <fieldset class="bp-card-details" id="bpCardDetails" style="display: none;" aria-hidden="true">
        <legend class="sr-only">Card details</legend>

        <div class="bp-field">
          <label for="bp-card-number">Card Number</label>
          <input type="text" id="bp-card-number" name="card_number" inputmode="numeric" placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number">
          <div class="bp-error" aria-live="polite"></div>
        </div>

        <div class="bp-row">
          <div class="bp-field">
            <label for="bp-expiry">Expiry Date</label>
            <input type="text" id="bp-expiry" name="expiry" inputmode="numeric" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
            <div class="bp-error" aria-live="polite"></div>
          </div>
          <div class="bp-field">
            <label for="bp-cvv">CVV</label>
            <input type="text" id="bp-cvv" name="cvv" inputmode="numeric" placeholder="123" maxlength="4" autocomplete="cc-csc">
            <div class="bp-error" aria-live="polite"></div>
          </div>
        </div>

        <div class="bp-field">
          <label for="bp-cardholder">Cardholder Name</label>
          <input type="text" id="bp-cardholder" name="cardholder" placeholder="John Doe" autocomplete="cc-name">
          <div class="bp-error" aria-live="polite"></div>
        </div>
      </fieldset>

      <!-- Contact Information -->
      <div class="bp-field">
        <label for="bp-phone">Contact Phone</label>
        <input type="tel" id="bp-phone" name="phone" inputmode="tel" placeholder="+94 77 123 4567" autocomplete="tel">
        <div class="bp-error" aria-live="polite"></div>
      </div>

      <!-- Billing Address -->
      <div class="bp-field">
        <label for="bp-billing-address">Billing Address</label>
        <textarea id="bp-billing-address" name="billing_address" placeholder="Enter your complete billing address" rows="3" autocomplete="street-address"></textarea>
        <div class="bp-error" aria-live="polite"></div>
      </div>

      <!-- Terms and Conditions -->
      <div class="bp-checkbox-field">
        <input type="checkbox" id="bp-terms" name="terms">
        <label for="bp-terms">I agree to the Terms and Conditions and Privacy Policy</label>
        <div class="bp-error" aria-live="polite"></div>
      </div>

      <!-- Security Notice -->
      <div class="bp-security-notice" aria-hidden="false">
        <div class="bp-security-icon" aria-hidden="true">🔒</div>
        <div>Your payment information is encrypted and secure</div>
      </div>
    </div>

    <!-- Footer (kept outside scrollable body so buttons remain visible) -->
    <div class="bp-footer">
      <button type="button" class="bp-cancel" id="bpCancel">Cancel</button>
      <button type="submit" class="bp-pay">PAY NOW</button>
    </div>
  </form>
</aside>

<noscript>
  <div class="bp-noscript">Please enable JavaScript to complete the booking.</div>
</noscript>
