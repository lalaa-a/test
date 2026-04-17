(function () {
    const navLinks = document.querySelectorAll('.nav-tab-link');
    const baseUrl = `${window.location.origin}/test/admin/`;

    loadSubtabContent('subtabHelpdesk');

    navLinks.forEach((link) => {
        link.addEventListener('click', async function (e) {
            e.preventDefault();

            navLinks.forEach((navLink) => navLink.classList.remove('active'));
            this.classList.add('active');

            await loadSubtabContent(this.dataset.tab);
        });
    });

    async function loadSubtabContent(subtabId) {
        const tabElement = document.getElementById('content-subtab-loader');

        tabElement.innerHTML = `
            <div style="text-align: center; padding: 40px; color: var(--primary);">
                <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 15px;"></i>
                <p>Loading ${subtabId} content...</p>
            </div>
        `;

        try {
            const response = await fetch(`${baseUrl}${subtabId}`);
            const { ok, loadingContent, message } = await response.json();

            if (!response.ok || !ok) {
                throw new Error(message || `Failed to load ${subtabId}`);
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

