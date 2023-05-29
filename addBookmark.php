<?php
include('functions.php');
include('static/Konst.php');
require('Logger.php');

session_start();

/* initial loeschen wir ein moegliches Cookie */
deleteAddCookie();

if ($_SESSION[SESSION_LOGIN] != OKAY) {
  Logger::trace('Login falsch! Weiterleitung zu Header-Location->' . PROJECT_STARTPAGE);

  if (isset($_GET[GET_TITLE]) && isset($_GET[GET_URL])) {
    createAddBookmarkCookie($_GET[GET_TITLE], $_GET[GET_URL]);
  }

  header("Location: " . PROJECT_STARTPAGE);
} else {
  deleteAddCookie();
}

$userdata = $_SESSION[SESSION_USERDATA];

Logger::trace("Login: ID=" . json_encode($userdata));

if (isset($_POST[POST_CATEGORYID]) && isset($_POST[POST_NAME]) && isset($_POST[POST_URL])) {
  $addCatId = $_POST[POST_CATEGORYID];
  $addTitle = $_POST[POST_NAME];
  $addUrl = $_POST[POST_URL];
  $addTags = $_POST[POST_TAGS];

  Logger::trace("POST->addCatId: " . $addCatId . ", addTitle: " . $addTitle . " addUrl: " . $addUrl);

  $addResult = addNewBookmark($addTitle, $addUrl, $addTags, $addCatId, $userdata[FIELD_ID]);

  if ($addResult) {
    echo '<script type="text/JavaScript">window.close()</script>';
  } else {
    showError('Fehler beim Hinzufügen des Bookmarks!');
  }
}

$title = "";
$url = "";

if (isset($_GET[GET_TITLE])) {
  $title = $_GET[GET_TITLE];
  Logger::trace("GET->title: " . $title);
}

if (isset($_GET[GET_URL])) {
  $url = $_GET[GET_URL];
  Logger::trace("GET->url: " . $url);
}

$categories = getCategorieListByUserId($userdata[FIELD_ID], true);
$categories[] = ['ID' => '0', 'NAME' => 'Default', 'USERID' => $userdata[FIELD_ID], 'COLOR' => '#ff5733'];

$sorted = array_column($categories, FIELD_NAME);
array_multisort($sorted, SORT_ASC, $categories);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title><?=PROJECTSHORTDESC;?></title>
  <meta name="description" content="<?=PROJECTSHORTDESC;?>">
  <link rel="icon" href="images/logo.png" type="image/png">
  <?=GOOGLE_FONTS;?>
  <link rel="stylesheet" href="<?=STYLE_DEFAULT_CSS;?>">
  <link rel="stylesheet" href="<?=STYLE_BOOTSTRAPMIN;?>">
  <link rel="stylesheet" href="<?=STYLE_FONTAWESOMEMIN;?>">
  <link rel="stylesheet" href="<?=STYLE_FONTAWESOMEALLMIN;?>">
  <script src="<?=JS_JQUERY_360;?>"></script>
  <script src="<?=JS_DEFAULT;?>"></script>
  <script>
      $(document).ready(function () {
          $(document).keydown(function (e) {
              // ESCAPE key pressed
              if (e.keyCode === 27) {
                  window.close();
              }
          });
      });

      function addCategory(val) {
          document.getElementById('categoryid').value = val;
      }
  </script>
  <style>
      body {
          background-color: #ffffff;
          color: #313131;
      }

      button, input {
          overflow: visible;
          width: 400px;
      }

      .addcat:hover {
          color: red !important;
          cursor: pointer;
      }

      .popup-form .cat-dropdown {
          width: 400px;
          height: 30px;
      }

      .submit-cancel-panel {
          width: 400px;
          float: left;
      }

      .left {
          width: 250px;
      }

      .right {
          width: 550px;
          float: left;
      }

      .row {
          margin: 10px 0;
          width: 600px;
      }

      .popup-headline-container {
          margin: 30px;
      }

      .popup-subheader {
          margin: 30px;
      }

      input {
          font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
      }

      .popup-headline {
          padding-left: 60px;
          margin-top: -45px;
      }
  </style>
</head>
<body>

<div class="container" style="width:800px;margin-top:25px;">

  <div class="popup-headline-container">
    <div class="fp-logo">
      <img src="./images/logo.png" width="50px" alt="<?=PROJECTNAME;?>" title="<?=PROJECTNAME;?>">
      <h1 class="popup-headline">Bookmark hinzuf&uuml;gen</h1>
    </div>

  </div>

  <div class="popup-subheader">
    <p>Hallo <?=$userdata[FIELD_NAME];?>!<br>
      Hier kannst du nun ganz einfach ein neues Bookmark hinzuf&uuml;gen. Nur die Felder ausf&uuml;llen, Kategorie ausw&auml;len, fertig!</p>
  </div>

  <div class="popup-form">

    <form action="addBookmark.php?done=true" method="post">
      <input type="hidden" name="userid" id="userid" value="<?=$userdata[FIELD_ID];?>">
      <div class="container">

        <div class="row">
          <div class="col left">
            <label for="name">Name:</label>
          </div>

          <div class="col right">
            <input autocomplete="off" type="text" name="name" id="name" value="<?=$title;?>"/>
          </div>
        </div>


        <div class="row">
          <div class="col left">
            <label for="url">URL:</label>
          </div>

          <div class="col right">
            <input autocomplete="off" type="text" name="url" id="url" value="<?=$url;?>"/>
          </div>
        </div>

        <div class="row">
          <div class="col left">
            <label for="tags">Tags:</label>
          </div>

          <div class="col right">
            <input type="text" name="tags" id="tags" placeholder="suchen,musik"/>
          </div>
        </div>

        <div class="row">
          <div class="col left">
            <label for="categoryid">Kategorie:</label>
          </div>

          <div class="col right">
            <select id="categoryid" name="categoryid" class="cat-dropdown">
              <?php
              foreach ($categories as $cat) {
                echo '<option value="' . $cat[FIELD_ID] . '">' . $cat[FIELD_NAME] . '</option>';
              }?>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col left">
            &nbsp;
          </div>

          <input type="hidden" name="done" value="true">

          <div class="col right">
            <div class="submit-cancel-panel">
              <button class="btn btn-submit" type="submit" id="submitNewBookmark" title="Bookmark hinzufügen">Hinzufügen</button>
            </div>
          </div>

        </div>
      </div>
    </form>
  </div>

</div>
</body>
</html>
