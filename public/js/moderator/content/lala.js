async loadTravelSpotCards(){
            try {

                let travelSpotCardDataTemp = [];
                const response = await fetch(this.URL_ROOT + '/Moderator/getTravelSpotCardData');
                const data = await response.json();

                console.log('this is travel spot data: ',data);
                
                if (data.success) {
                    
                    travelSpotCardDataTemp = data.travelSpotCardData;
                    console.log("all filters ",travelSpotCardData);

                    // Clear the array before re-populating to avoid duplicates
                    this.travelSpotCardData = [];

                    //Grouping elemets 
                    travelSpotCardDataTemp.forEach(item => {
                        // check if the main filter already exist
                        let existing = this.travelSpotCardData.find(g => g.mainFilterId === item.mainFilterId);
                        let index = this.travelSpotCardData.findIndex(g => g.mainFilterId === item.mainFilterId);
                        // if not, create it
                        if (!existing) {

                            existing = {
                                mainFilterId: item.mainFilterId,
                                mainFilterName: item.mainFilterName,
                                travelSpots:[]
                            };

                            let spotData = {
                                            spotId: item.spotId,
                                            spotName: item.spotName,
                                            overview: item.overview,
                                            totalReviews: item.totalReviews,
                                            averageRating: item.averageRating,
                                            subFilterId: item.subFilterId,
                                            subFilterName: item.subFilterName,
                                            photoPath:item.photoPath
                                        };
                            existing.travelSpots.push(spotData);
                            this.travelSpotCardData.push(existing);

                        } else {

                            let existingSpot = existing.travelSpots.find(g => g.spotId === item.spotId);
                            if(!existingSpot){
                                
                                existingSpot =  {
                                                    spotId: item.spotId,
                                                    spotName: item.spotName,
                                                    overview: item.overview,
                                                    totalReviews: item.totalReviews,
                                                    averageRating: item.averageRating,
                                                    subFilterId: item.subFilterId,
                                                    subFilterName: item.subFilterName
                                                };
                                this.travelSpotCardData[index].travelSpots.push(existingSpot);
                            }
                        }
                    });

                console.log(this.travelSpotCardData);
                this.renderTravelSpotCards();

                } else {
                    console.error('Failed to load travel apot cards :', data.message);
                    alert('Failed to load travel spot cards: ' + data.message);
                }
                    
            } catch (error) {
                console.error('Error loading travel spot cards:', error);
                alert('EError loading travel spot cards ' + error.message);
            }  
        }