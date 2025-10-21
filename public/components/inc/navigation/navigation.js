document.addEventListener('DOMContentLoaded', function () {
  var tripsBtn = document.getElementById('tripsButton');
  if (tripsBtn) {
    tripsBtn.addEventListener('click', function () {
      window.location.href = '/test/index.php?url=User/trips';
    });
  }
});
