<!-- MODAL WINDOW -->
<div id="uploadModal" class="modalupload">
  <div class="modalupload-content">
    <span style="text-align:right;" class="closeUpload">&times;</span>
    <p>Hier kannst du deine Favoriten-Datei aus deinem Browser hochladen um deine Lesezeichen zu importieren.</p>
    <p><b>Wird mit einer der nächsten Versionen implementiert</b></p>
  </div>
</div>
<script>
    // Get the modal
    const modalupload = document.getElementById("uploadModal");

    // Get the button that opens the modal
    const btnUpload = document.getElementById("uploadBtn");

    // Get the <span> element that closes the modal
    const spanUp = document.getElementsByClassName("closeUpload")[0];

    // When the user clicks the button, open the modal
    btnUpload.onclick = function() {
        modalupload.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    spanUp.onclick = function() {
        modalupload.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modalupload) {
            modalupload.style.display = "none";
        }
    }
</script>
