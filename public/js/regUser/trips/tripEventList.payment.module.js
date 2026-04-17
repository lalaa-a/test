(function () {
    function applyTripEventListPaymentModule(TripEventListManager) {
        if (!TripEventListManager || !TripEventListManager.prototype) {
            return;
        }

        TripEventListManager.prototype.submitTripPaymentForm = function (checkoutUrl, formFields) {
            if (!checkoutUrl || !formFields || typeof formFields !== 'object') {
                throw new Error('Invalid checkout payload');
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = checkoutUrl;
            form.style.display = 'none';

            Object.keys(formFields).forEach((key) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = formFields[key] == null ? '' : String(formFields[key]);
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        };

        TripEventListManager.prototype.startTripPayment = async function () {
            const tripId = this.tripId?.textContent;
            if (!tripId) {
                alert('Unable to process payment without a valid trip ID.');
                return;
            }

            if (this.confirmTripBtn) {
                this.confirmTripBtn.disabled = true;
                this.confirmTripBtn.textContent = 'Processing...';
            }

            try {
                const response = await fetch(`${this.URL_ROOT}/RegUser/initiateTripPayment/${tripId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ tripId: Number(tripId) })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Unable to initiate payment');
                }

                this.submitTripPaymentForm(result.checkoutUrl, result.formFields);
            } catch (error) {
                console.error('Trip payment initialization failed:', error);
                alert(error.message || 'Failed to initiate payment. Please try again.');
                await this.fetchTripStatus();
            } finally {
                if (this.confirmTripBtn) {
                    this.updateTripStatusState(this.tripStatus);
                }
            }
        };

        TripEventListManager.prototype.handleTripPaymentReturnStatus = async function () {
            const params = new URLSearchParams(window.location.search);
            const paymentState = params.get('paymentState');
            const paymentTripId = Number(params.get('paymentTripId'));
            const currentTripId = Number(this.tripId?.textContent || 0);

            if (!paymentState || !paymentTripId || paymentTripId !== currentTripId) {
                return;
            }

            if (paymentState === 'return') {
                await this.fetchTripStatus();

                if (this.tripStatus === 'scheduled') {
                    alert('Payment completed successfully. Your trip is now scheduled.');
                } else {
                    alert('Payment submitted. Waiting for gateway confirmation.');
                }
            } else if (paymentState === 'cancel') {
                alert('Payment was cancelled. You can retry from the trip summary.');
            }

            params.delete('paymentState');
            params.delete('paymentTripId');
            const query = params.toString();
            const cleanUrl = `${window.location.pathname}${query ? `?${query}` : ''}${window.location.hash || ''}`;
            window.history.replaceState({}, document.title, cleanUrl);
        };

        const originalLoadExistingDrivers = TripEventListManager.prototype.loadExistingDrivers;
        if (typeof originalLoadExistingDrivers === 'function') {
            TripEventListManager.prototype.loadExistingDrivers = async function (...args) {
                const result = await originalLoadExistingDrivers.apply(this, args);
                await this.handleTripPaymentReturnStatus();
                return result;
            };
        }
    }

    window.applyTripEventListPaymentModule = applyTripEventListPaymentModule;
})();
