(function(){
      const openBtn = document.getElementById("openAddLocation");
      const panel = document.getElementById('alPanel');
      const overlay = document.getElementById('alOverlay');
      const cancelBtn = document.getElementById('alCancel');
      const form = document.getElementById('alForm');

      function openPanel(){
        panel.classList.add('active');
        overlay.classList.add('active');
      }

      function closePanel(){
        panel.classList.remove('active');
        overlay.classList.remove('active');
      }

      // Open panel
      openBtn.addEventListener('click', openPanel);

      // Close panel
      cancelBtn.addEventListener('click', closePanel);
      overlay.addEventListener('click', closePanel);

      // Form validation
      form.addEventListener('submit', function(e){
        e.preventDefault();
        let valid = true;

        // Clear old errors
        form.querySelectorAll('.al-error').forEach(el => el.textContent = '');

        const type = document.getElementById('al-type');
        const start = document.getElementById('al-start');
        const end = document.getElementById('al-end');
        const notes = document.getElementById('al-notes');
        const location = document.getElementById('al-location');

        if (!type.value) {
          type.nextElementSibling.textContent = 'Please select a location type';
          valid = false;
        }
        if (!start.value) {
          start.nextElementSibling.textContent = 'Please enter a start time';
          valid = false;
        }
        if (!end.value) {
          end.nextElementSibling.textContent = 'Please enter an end time';
          valid = false;
        }
        if (!notes.value.trim()) {
          notes.nextElementSibling.textContent = 'Please enter notes';
          valid = false;
        }
        if (!location.value.trim()) {
          location.nextElementSibling.textContent = 'Please enter a location';
          valid = false;
        }

        if (valid) {
          alert('Form submitted successfully!');
          closePanel();
          form.reset();
        }
      });
    })();