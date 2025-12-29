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
            this.confirmModel.style.display = 'flex';
            this.selectingSpot.innerHTML = spotName;
        }

        hideModal() {
            this.confirmModel.style.display = 'none';
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
                    window.opener.tripEventListManager.handleSpotSelection(spotId);
                    window.close();
                }
            } else {
                window.opener.postMessage({
                    type: 'CARD_SELECTED',
                    cardData: cardData
                }, window.opener.location.origin);
            }
        } 
        
    }

    window.SpotSelectionManager = SpotSelectionManager;
    window.spotSelectionManager = new SpotSelectionManager();
})();