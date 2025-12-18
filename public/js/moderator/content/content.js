(function() {

    const navLinks = document.querySelectorAll('.nav-tab-link');

    navLinks.forEach(link => {
            
        link.addEventListener('click', async function(e) {

            e.preventDefault();

            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));

            console.log(link.dataset.tab); 

            // Add active class to clicked link
            this.classList.add('active');

            await loadSubtabContent(link.dataset.tab);

            // Here you can add logic to load different content sections
            console.log('Navigating to:', this.getAttribute('data-tab'));

        });
    });

    setTimeout(() => {
        loadSubtabContent('subtabTravelSpots');
    }, 1000);


    async function loadSubtabContent(subtabId){

        const tabElement = document.getElementById('content-subtab-loader');
        
        // Show loading state
        tabElement.innerHTML = `
            <div style="text-align: center; padding: 40px; color: var(--primary);">
                <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 15px;"></i>
                <p>Loading ${subtabId} content...</p>
            </div>
        `;

        try{     

            const {ok,loadingContent} = await fetch('http://localhost/test/Moderator/'+`${subtabId}`).then(r=>r.json());

            if (!ok) {
                throw new Error(`Failed to load ${tabId}: ${response.status}`);
            }

            cleanupPreviousAssets(subtabId);
                    
            tabElement.innerHTML =  loadingContent.html;
                    
            if(loadingContent.css){
                appendCSS(loadingContent.css,subtabId)
            }

            if(loadingContent.js){
                appendJS(loadingContent.js,subtabId);
            }

        }
        catch (error) {
            console.error('Error loading tab:', error);
            tabElement.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #dc3545;">
                    <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 15px;"></i>
                    <h3>Failed to load content</h3>
                    <p>${error.message}</p>
                    
                    </button>
                </div>
            `;
        }
    }

})();

