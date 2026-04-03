(function(){
    // Check if TravelSpotManager already exists and clean up
    if (window.SpotSelectionManager) {
        console.log('TravelSpotManger already exists, cleaning up...');
        // Clean up any existing instance
        if (window.spotSelectionManager) {
            // Clean up event listeners if needed
            delete window.spotSelectionManager;
        }
        delete window.SpotSelectionManager;
    }
    
    class SpotSelectionManager {

        constructor() {
            this.selectedSpotId = null;
            this.selectedCard = null;
            this.selectedSpotName = null;

            this.initializeElements();
            this.addEventListeners();

        }

        initializeElements(){
            this.confirmModel = document.getElementById('confirmationModal');
            this.selectingSpot = document.getElementById('selecting-spot-name');
            this.confirmBtn = document.getElementById('confirmBtn');
            this.cancelBtn = document.getElementById("cancelBtn");
        }

        addEventListeners(){

            this.confirmBtn.addEventListener('click', () => {
                this.selectSpot(this.selectedSpotId);
                this.hideModal();
            } )

            this.cancelBtn.addEventListener('click', () => this.hideModal());
        }

        showConfirmation(spotId, spotName) {
            this.selectedSpotId = spotId;
            this.selectedSpotName = spotName;
            this.selectedCard = document.getElementById(`spot-${spotId}`);
            if (this.selectedCard) {
                this.selectedCard.classList.add('selected');
            }
            this.confirmModel.classList.add('show');
            this.selectingSpot.innerHTML = spotName;
        }

        hideModal() {
            this.confirmModel.classList.remove('show');
            if (this.selectedCard) {
                this.selectedCard.classList.remove('selected');
            }
            this.selectedSpotId = null;
            this.selectedCard = null;
        }

        selectSpot(spotId) {
            console.log('Selected spot with ID:', spotId);

            if (window.opener && !window.opener.closed) {
                if (window.location.origin === window.opener.location.origin) {
                    console.log('Same origin, calling function directly.');

                    // Get spot data for the selected spot
                    const spotData = this.getSpotData(spotId);

                    // Check if called from guide spots or trip events
                    if (window.opener.guideSpotsManager) {
                        // Called from guide spots page
                        window.opener.guideSpotsManager.handleSpotSelection(spotData);
                    } else if (window.opener.tripEventListManager) {
                        // Called from trip events page
                        window.opener.tripEventListManager.handleSpotSelection(spotId);
                    }

                    window.close();
                }
            } else {
                window.opener.postMessage({
                    type: 'CARD_SELECTED',
                    cardData: cardData
                }, window.opener.location.origin);
            }
        }

        getSpotData(spotId) {
            // Find the spot card and extract its data
            const spotCard = document.getElementById(`spot-${spotId}`);
            if (spotCard) {
                const spotName = spotCard.querySelector('.place-title').textContent.trim();
                return {
                    id: spotId,
                    name: spotName
                };
            }

            // Fallback if card not found
            return {
                id: spotId,
                name: this.selectedSpotName || `Spot ${spotId}`
            };
        } 
        
    }

    window.SpotSelectionManager = SpotSelectionManager;
    window.spotSelectionManager = new SpotSelectionManager();
})();