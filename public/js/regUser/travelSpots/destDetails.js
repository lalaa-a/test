(function () {
    const mainPhoto = document.getElementById('mainSpotPhoto');
    const thumbContainer = document.getElementById('spotPhotoThumbs');

    if (!mainPhoto || !thumbContainer) {
        return;
    }

    thumbContainer.addEventListener('click', function (event) {
        const thumbButton = event.target.closest('.photo-thumb-btn');
        if (!thumbButton) {
            return;
        }

        const photoUrl = thumbButton.getAttribute('data-photo-url');
        if (!photoUrl) {
            return;
        }

        mainPhoto.src = photoUrl;

        thumbContainer.querySelectorAll('.photo-thumb-btn').forEach(function (button) {
            button.classList.remove('active');
        });

        thumbButton.classList.add('active');
    });
})();
