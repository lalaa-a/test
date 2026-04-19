(function() {
    const URL_ROOT = `${window.location.origin}/test`;
    const navLinks = document.querySelectorAll('.nav-tab-link');

    if (!navLinks.length) {
        return;
    }

    loadSubtabContent('subtabHelpdesk');

    navLinks.forEach((link) => {
        link.addEventListener('click', async function(e) {
            e.preventDefault();

            navLinks.forEach((item) => item.classList.remove('active'));
            this.classList.add('active');

            await loadSubtabContent(link.dataset.tab);
        });
    });

    async function loadSubtabContent(subtabId) {
        const tabElement = document.getElementById('content-subtab-loader');
        if (!tabElement) {
            return;
        }

        tabElement.innerHTML = `
            <div style="text-align: center; padding: 40px; color: var(--primary);">
                <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 15px;"></i>
                <p>Loading ${subtabId} content...</p>
            </div>
        `;

        try {
            const response = await fetch(`${URL_ROOT}/RegUser/support/${subtabId}`);
            const data = await response.json();
            const { ok, loadingContent } = data;

            if (!response.ok || !ok || !loadingContent) {
                throw new Error(`Failed to load ${subtabId} content`);
            }

            cleanupPreviousAssets(subtabId);

            tabElement.innerHTML = loadingContent.html;

            if (loadingContent.css) {
                appendCSS(loadingContent.css, subtabId);
            }

            if (loadingContent.js) {
                appendJS(loadingContent.js, subtabId);
            }
        } catch (error) {
            console.error('Error loading tab:', error);
            tabElement.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #dc3545;">
                    <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 15px;"></i>
                    <h3>Failed to load content</h3>
                    <p>${error.message}</p>
                </div>
            `;
        }
    }
})();

