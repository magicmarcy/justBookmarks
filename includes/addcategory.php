<!-- MODAL WINDOW -->
<div id="addCatModal" class="modalAddCat">
  <form class="modalAddCat-content">
    <span style="text-align:right;" class="closeAddCat">&times;</span>

    <p><b>Neue Kategorie hinzufügen</b></p>
    <div class="container">

    <form action="<?=DASHBOARD;?>" method="POST">
      <div class="form-group">
        <label for="category" class="col-sm-2 col-form-label" style="min-width: 800px;">Kategoriename: <small>(max. <?=CAT_MAX_CHARS;?> Zeichen, keine Sonderzeichen)</small></label>
        <div class="col-sm-10">
          <input type="text" style="width:600px;" class="form-control" id="category" name="category" maxlength="<?=CAT_MAX_CHARS;?>" placeholder="" value="">
        </div>
      </div>

      <div class="form-group">
        <label for="parentcategoryid" class="col-sm-2 col-form-label" style="min-width: 800px;">&Uuml;bergeordnete Kategorie:</label>
        <div class="col-sm-10">
          <select id="parentcategoryid" style="width:600px;" name="parentcategoryid" class="cat-dropdown">
            <?php
            $categories = getCategorieListByUserId($userdata[FIELD_ID], true);
            $categories[] = ['ID' => '0', 'NAME' => 'Keine', 'USERID' => $userdata[FIELD_ID], 'COLOR' => '#ff5733'];

            foreach ($categories as $cat) {
              if (isSubSubCategory($cat[FIELD_ID], $userdata[FIELD_ID])) {
                Logger::trace("Kategorie '" . $cat[FIELD_NAME] . "' ist SubSubCategory und wird uebersprungen...");
                continue;
              }

              if ($cat[FIELD_ID] == '0') {
                echo '<option value="' . $cat[FIELD_ID] . '" selected="true">' . $cat[FIELD_NAME] . '</option>';
              } else {
                echo '<option value="' . $cat[FIELD_ID] . '">' . $cat[FIELD_NAME] . '</option>';
              }
            }?>
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
    if (btnAddCat && modalAddCat) {
        btnAddCat.onclick = function () {
            modalAddCat.style.display = "block";
        }
    }

    // When the user clicks on <span> (x), close the modal
    if (spanCat && modalAddCat) {
        spanCat.onclick = function () {
            modalAddCat.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    if (modalAddCat) {
        window.onclick = function (event) {
            if (event.target === modalAddCat) {
                modalAddCat.style.display = "none";
            }
        }
    }
</script>
