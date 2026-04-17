(function () {
    function applyTripEventListRouteMapModule(TripEventListManager) {
        if (!TripEventListManager || !TripEventListManager.prototype) {
            return;
        }

        // Initialize the route map
        TripEventListManager.prototype.initializeRouteMap = async function () {
            try {
                // Check if the new importLibrary method is available
                if (google.maps.importLibrary) {
                    const { Map } = await google.maps.importLibrary("maps");

                    this.routeMap = new Map(document.getElementById("route-map"), {
                        center: { lat: 7.8731, lng: 80.7718 }, // Center of Sri Lanka
                        zoom: 8,
                        mapId: "route_map_id"
                    });
                } else {
                    // Fallback to traditional Google Maps initialization
                    this.routeMap = new google.maps.Map(document.getElementById("route-map"), {
                        center: { lat: 7.8731, lng: 80.7718 }, // Center of Sri Lanka
                        zoom: 8
                    });
                }

                this.directionsService = new google.maps.DirectionsService();
                this.directionsRenderer = new google.maps.DirectionsRenderer({
                    map: this.routeMap,
                    suppressMarkers: true, // We'll add custom markers
                    polylineOptions: {
                        strokeColor: '#006a71',
                        strokeWeight: 4,
                        strokeOpacity: 0.7
                    }
                });

                console.log('Route map initialized successfully');
            } catch (error) {
                console.error('Error initializing route map:', error);
            }
        };

        // Update the route map with event coordinates
        TripEventListManager.prototype.updateRouteMap = async function (eventDate) {
            if (!this.routeMap) {
                console.warn('Route map not initialized');
                return;
            }

            try {
                // Fetch coordinates for events on this date
                const response = await fetch(`${this.URL_ROOT}/RegUser/getEventCoordinates/${this.tripId.textContent}/${eventDate}`);
                const data = await response.json();

                if (!data.success) {
                    console.error('Failed to fetch coordinates:', data.message);
                    return;
                }

                const coordinates = data.coordinates;
                console.log('Fetched coordinates:', coordinates);

                // Clear existing markers
                this.clearRouteMarkers();

                if (coordinates.length === 0) {
                    console.log('No coordinates to display');
                    return;
                }

                // Create waypoints for directions
                if (coordinates.length >= 2) {
                    await this.renderDirections(coordinates);
                } else if (coordinates.length === 1) {
                    // Single location - just add a marker
                    await this.addSingleMarker(coordinates[0]);
                }

            } catch (error) {
                console.error('Error updating route map:', error);
            }
        };

        // Render directions with waypoints
        TripEventListManager.prototype.renderDirections = async function (coordinates) {

            // Origin is the first coordinate
            const origin = { lat: coordinates[0].lat, lng: coordinates[0].lng };

            // Destination is the last coordinate
            const destination = {
                lat: coordinates[coordinates.length - 1].lat,
                lng: coordinates[coordinates.length - 1].lng
            };

            // Waypoints are everything in between
            const waypoints = coordinates.slice(1, -1).map(coord => ({
                location: { lat: coord.lat, lng: coord.lng },
                stopover: true
            }));

            const request = {
                origin: origin,
                destination: destination,
                waypoints: waypoints,
                travelMode: google.maps.TravelMode.DRIVING,
                optimizeWaypoints: false // Keep the order as scheduled
            };

            try {
                const result = await this.directionsService.route(request);
                this.directionsRenderer.setDirections(result);

                // Add custom markers for each location
                coordinates.forEach((coord, index) => {
                    const marker = new google.maps.Marker({
                        map: this.routeMap,
                        position: { lat: coord.lat, lng: coord.lng },
                        label: {
                            text: String(index + 1),
                            color: 'white',
                            fontWeight: 'bold'
                        },
                        title: coord.name,
                        icon: {
                            path: google.maps.SymbolPath.CIRCLE,
                            scale: 12,
                            fillColor: '#006a71',
                            fillOpacity: 1,
                            strokeColor: 'white',
                            strokeWeight: 3
                        }
                    });

                    // Add info window
                    const infoWindow = new google.maps.InfoWindow({
                        content: `<div style="padding: 5px;"><strong>${this.escapeHtml(coord.name)}</strong></div>`
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(this.routeMap, marker);
                    });

                    this.routeMarkers.push(marker);
                });

            } catch (error) {
                console.error('Error rendering directions:', error);
                // Fallback: just show markers with polyline
                this.renderMarkersWithPolyline(coordinates);
            }
        };

        // Fallback: render markers with a simple polyline
        TripEventListManager.prototype.renderMarkersWithPolyline = async function (coordinates) {

            // Add markers
            coordinates.forEach((coord, index) => {
                const marker = new google.maps.Marker({
                    map: this.routeMap,
                    position: { lat: coord.lat, lng: coord.lng },
                    label: {
                        text: String(index + 1),
                        color: 'white',
                        fontWeight: 'bold'
                    },
                    title: coord.name,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 12,
                        fillColor: '#006a71',
                        fillOpacity: 1,
                        strokeColor: 'white',
                        strokeWeight: 3
                    }
                });

                // Add info window
                const infoWindow = new google.maps.InfoWindow({
                    content: `<div style="padding: 5px;"><strong>${this.escapeHtml(coord.name)}</strong></div>`
                });

                marker.addListener('click', () => {
                    infoWindow.open(this.routeMap, marker);
                });

                this.routeMarkers.push(marker);
            });

            // Draw polyline
            const path = coordinates.map(coord => ({ lat: coord.lat, lng: coord.lng }));

            this.routePath = new google.maps.Polyline({
                path: path,
                geodesic: true,
                strokeColor: '#006a71',
                strokeOpacity: 0.7,
                strokeWeight: 4,
                map: this.routeMap
            });

            // Fit bounds to show all markers
            const bounds = new google.maps.LatLngBounds();
            coordinates.forEach(coord => {
                bounds.extend({ lat: coord.lat, lng: coord.lng });
            });
            this.routeMap.fitBounds(bounds);
        };

        // Add a single marker
        TripEventListManager.prototype.addSingleMarker = async function (coord) {

            const marker = new google.maps.Marker({
                map: this.routeMap,
                position: { lat: coord.lat, lng: coord.lng },
                label: {
                    text: '1',
                    color: 'white',
                    fontWeight: 'bold'
                },
                title: coord.name,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 12,
                    fillColor: '#006a71',
                    fillOpacity: 1,
                    strokeColor: 'white',
                    strokeWeight: 3
                }
            });

            // Add info window
            const infoWindow = new google.maps.InfoWindow({
                content: `<div style="padding: 5px;"><strong>${this.escapeHtml(coord.name)}</strong></div>`
            });

            marker.addListener('click', () => {
                infoWindow.open(this.routeMap, marker);
            });

            this.routeMarkers.push(marker);

            // Center map on this marker
            this.routeMap.setCenter({ lat: coord.lat, lng: coord.lng });
            this.routeMap.setZoom(12);
        };

        // Clear all route markers and paths
        TripEventListManager.prototype.clearRouteMarkers = function () {
            // Clear markers
            this.routeMarkers.forEach(marker => {
                marker.setMap(null);
            });
            this.routeMarkers = [];

            // Clear polyline
            if (this.routePath) {
                this.routePath.setMap(null);
                this.routePath = null;
            }

            // Clear directions
            if (this.directionsRenderer) {
                this.directionsRenderer.setDirections({ routes: [] });
            }
        };
    }

    window.applyTripEventListRouteMapModule = applyTripEventListRouteMapModule;
})();
