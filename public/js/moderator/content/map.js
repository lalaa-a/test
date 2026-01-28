
<div id="autocomplete-container"></div>
<button id="save-button" disabled onclick="saveLocation()">Save Location</button>
<div id="map" style="width: 100%; height: 400px;"></div>

initMap();

async function initMap() {

    let map;
    let marker;
    let selectedPlace = null;

    const { Map } = await google.maps.importLibrary("maps");
    const { PlaceAutocompleteElement } = await google.maps.importLibrary("places");

    map = new Map(document.getElementById("map"), {
        center: { lat: -34.397, lng: 150.644 },
        zoom: 8,
        mapId: "12b46b4ecb983b59bcc14db4"
    });

    const saveButton = document.getElementById("save-button");

    // Create PlaceAutocompleteElement
    const placeAutocomplete = new PlaceAutocompleteElement();
    
    // Insert after your input or in a specific container
    const container = document.getElementById("autocomplete-container");
    container.appendChild(placeAutocomplete);

    console.log("PlaceAutocomplete appended to DOM i");

    placeAutocomplete.addEventListener("gmp-select", async ({ placePrediction }) => {

        const place = placePrediction.toPlace();

        await place.fetchFields({
            fields: ['displayName', 'formattedAddress', 'location'],
        });

        if (!place.location) {
            console.log("No location available");
            return;
        }

        if (marker) {
            marker.setMap(null);
        }

        //creating the advanced marker element
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

        marker = new AdvancedMarkerElement({
            map,
            position: place.location,
            title: place.displayName,
        });

        selectedPlace = {
            name: place.displayName,
            address: place.formattedAddress,
            lat: place.location.lat(),
            lng: place.location.lng()
        };
        saveButton.disabled = false;

        map.setCenter(place.location);
        map.setZoom(17);
    });
}

window.initMap = initMap;