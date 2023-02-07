<!-- MODAL WINDOW -->
<div id="addCatModal" class="modalAddCat">
  <form class="modalAddCat-content">
    <span style="text-align:right;" class="closeAddCat">&times;</span>

    <p><b>Neue Kategorie hinzufügen</b></p>
    <div class="container">

    <form action="<?php echo DASHBOARD;?>" method="POST">
      <div class="form-group">
        <label for="category" class="col-sm-2 col-form-label" style="min-width: 800px;">Kategoriename: <small>(max. 20 Zeichen, keine Sonderzeichen)</small></label>
        <div class="col-sm-10">
          <input type="text" style="width:600px;" class="form-control" id="category" name="category" maxlength="20" placeholder="" value="">
        </div>
      </div>

      <div class="form-group">
        <label for="parentcategoryid" class="col-sm-2 col-form-label" style="min-width: 800px;">&Uuml;bergeordnete Kategorie:</label>
        <div class="col-sm-10">
          <select id="parentcategoryid" style="width:600px;" name="parentcategoryid" class="cat-dropdown">
            <?php
            $categories = getCategorieListByUserId($userdata['ID'], false);
            array_push($categories, ['ID' => '0', 'NAME' => 'Keine', 'USERID' => $userdata['ID'], 'COLOR' => '#ff5733']);

            foreach ($categories as $cat) {
              if ($cat['ID'] == '0') {
                echo '<option value="' . $cat['ID'] . '" selected="true">' . $cat['NAME'] . '</option>';
              } else {
                echo '<option value="' . $cat['ID'] . '">' . $cat['NAME'] . '</option>';
              }
            }; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="color" class="col-sm-2 col-form-label">Farbe:</label>
        <div class="col-sm-10">
          <input type="color" class="colorpicker" id="color" name="color" placeholder="" value="#ff5733">
        </div>
      </div>

      <div class="form-group">
        <div class="col-sm-10">
          <input type="submit" class="button-newfile" value="Hinzufügen">
        </div>
      </div>
    </form>
</div>

  </div>
<script>
    // Get the modal
    const modalAddCat = document.getElementById("addCatModal");

    // Get the button that opens the modal
    const btnAddCat = document.getElementById("addCatBtn");

    // Get the <span> element that closes the modal
    const spanCat = document.getElementsByClassName("closeAddCat")[0];

    // When the user clicks the button, open the modal
    btnAddCat.onclick = function() {
        modalAddCat.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    spanCat.onclick = function() {
        modalAddCat.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modalAddCat) {
            modalAddCat.style.display = "none";
        }
    }
</script>
