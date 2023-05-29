<?php
$url =
<<<EOD
javascript:function p(a,w,h){var b=window.screenLeft!=undefined?window.screenLeft:screen.left;var c=window.screenTop!=undefined?window.screenTop:screen.top;width=window.innerWidth?window.innerWidth:document.documentElement.clientWidth?document.documentElement.clientWidth:screen.width;height=window.innerHeight?window.innerHeight:document.documentElement.clientHeight?document.documentElement.clientHeight:screen.height;var d=((width/2)-(w/2))+b;var e=((height/2)-(h/2))+c;var f=window.open(a,new Date().getTime(),'width='+w+', height='+h+', top='+e+', left='+d+'location=yes,resizable=yes,status=no,scrollbars=no,personalbar=no,toolbar=no,menubar=no');if(window.focus){f.focus()}}p('https://bookmarks.magicmarcy.de/addBookmark.php?title='+document.title+'&url='+document.URL,800,500);
EOD;
?>
<!-- MODAL WINDOW -->
<div id="bookmarkletModal" class="modalupload">
  <div class="modalbookmaklet-content">
    <div class="modal-close" title="Schließen"><a onclick="hideBookmarkletModal();">X</a></div>
    <div class="popup-headline-container">
      <div class="fp-logo">
        <h1 class="popup-headline"><img src="../images/logo.png" width="50px" alt="<?=PROJECTNAME;?>" title="<?=PROJECTNAME;?>"> justBookmarks Bookmarklet</h1>
        <hr />
      </div>
    </div>
    <p>Das <strong>justBookmarks Bookmarklet</strong> ist ein kleines JavaScript, welches du als Favorit/Lesezeichen in deinem Browser hinzuf&uuml;gen kannst um damit jede Webseite zu deinen Bookmarks in justBookmarks hinzuzuf&uuml;gen.</p>
    <p>Du kannst einfach den folgenden Button per Drag and Drop in deine Favoritenleiste des Browser ziehen - fertig!</p>

    <div class="bookmarklet">
      <a href="<?=$url?>" title="ToBookmark">
        <div class="bookmarklet-button">
          <img src="../images/logo.png" alt="" width="15px" style="padding-bottom:5px;"> ToBookmark
        </div>
      </a>
    </div>

    <br/>
    <p>Wenn du nun auf das Lesezeichen klickst, &ouml;ffnet sich ein Popup-Fenster, in dem der Titel und die URL der Webseite bereits eingetragen sind. W&auml;hle nun aus deinen Kategorien aus, wo das Bookmark gespeichert werden soll und klicke auf "Hinzuf&uuml;gen". Das Popup schließt sich automatisch und dein Bookmark ist gespeichert.</p>
    <p>Falls du gerade nicht eingeloggt bist, wirst du zun&auml;chst zum Login weitergeleitet. Die Angaben der Webseite (Titel und URL) werden solange in einem Cookie zwischengespeichert und du kannst nach dem Login mit dem Speichern fortfahren.</p>
  </div>
</div>
<script>
    // Get the modal
    const modalbookmarklet = document.getElementById("bookmarkletModal");

    // Get the button that opens the modal
    const btnBookmarklet = document.getElementById("bookmarkletBtn");

    // Get the <span> element that closes the modal
    const spanUp1 = document.getElementsByClassName("closeBookmarklet")[0];

    // When the user clicks the button, open the modal
    if (btnBookmarklet && modalbookmarklet) {
        btnBookmarklet.onclick = function () {
            modalbookmarklet.style.display = "block";
        }
    }

    // When the user clicks on <span> (x), close the modal
    if (spanUp1 && modalbookmarklet) {
        spanUp1.onclick = function () {
            modalbookmarklet.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    if (modalbookmarklet) {
        window.onclick = function (event) {
            if (event.target === modalbookmarklet) {
                modalbookmarklet.style.display = "none";
            }
        }
    }
</script>
