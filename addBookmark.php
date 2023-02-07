<?php
include('functions.php');
include('static/Konst.php');
require('Logger.php');

session_start();

/* initial loeschen wir ein moegliches Cookie */
deleteAddCookie();

if ($_SESSION['login'] != OKAY) {
  Logger::trace('main(): Login falsch! Weiterleitung zu Header-Location->' . PROJECT_STARTPAGE);

  if (isset($_GET["title"]) && isset($_GET["url"])) {
    createAddBookmarkCookie($_GET["title"], $_GET["url"]);
  }

  header("Location: " . PROJECT_STARTPAGE);
} else {
  deleteAddCookie();
}

$userdata = $_SESSION['userdata'];

Logger::trace("init(): Login: ID=" . $userdata['ID'] . " NANE=" . $userdata['NAME'] . " EMAIL=" . $userdata['EMAIL'] . " PASS=" . $userdata['PASS'] . " VERIFIED=" . $userdata['VERIFIED'] . " CREATED=" . $userdata['CREATED'] . " LASTLOGIN=" . $userdata['LASTLOGIN']);

if (isset($_POST['categoryid']) && isset($_POST['name']) && isset($_POST['url'])) {
  $addCatId = $_POST['categoryid'];
  $addTitle = $_POST['name'];
  $addUrl = $_POST['url'];
  $addTags = $_POST['tags'];

  Logger::trace("addBookmark(): POST->addCatId: " . $addCatId . ", addTitle: " . $addTitle . " addUrl: " . $addUrl);

  $addResult = addNewBookmark($addTitle, $addUrl, $addTags, $addCatId, $userdata['ID']);

  if ($addResult) {
    echo '<script type="text/JavaScript">window.close()</script>';
  } else {
    echo '<div id="alert" class="alert alert-info" role="alert">Fehler beim Hinzufügen des Bookmarks!</div>';
  }
}

$title = "";
$url = "";

if (isset($_GET["title"])) {
  $title = $_GET["title"];
  Logger::trace("addBookmark(): GET->title: " . $title);
}

if (isset($_GET["url"])) {
  $url = $_GET["url"];
  Logger::trace("addBookmark(): GET->url: " . $url);
}

$categories = getCategorieListByUserId($userdata['ID'], true);
array_push($categories, ['ID' => '0', 'NAME' => 'Default', 'USERID' => $userdata['ID'], 'COLOR' => '#ff5733']);

$sorted = array_column($categories, 'NAME');
array_multisort($sorted, SORT_ASC, $categories);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title><?php echo PROJECTSHORTDESC; ?></title>
  <meta name="description" content="<?php echo PROJECTSHORTDESC; ?>">
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Oleo+Script+Swash+Caps&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="externals/default.js"></script>
  <script>
      $(document).ready(function () {
          $(document).keydown(function (e) {
              // ESCAPE key pressed
              if (e.keyCode === 27) {
                  window.close();
              }
          });
      });

      setTimeout(function () {
          $('#alert').fadeOut('fast');
      }, <?php echo ALERT_TIMEOUT;?>);

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
      <img src="./images/logo.png" width="50px" alt="<?php echo PROJECTNAME; ?>" title="<?php echo PROJECTNAME; ?>">
      <h1 class="popup-headline">Bookmark hinzuf&uuml;gen</h1>
    </div>

  </div>

  <div class="popup-subheader">
    <p>Hallo <?php echo $userdata['NAME']; ?>!<br>
      Hier kannst du nun ganz einfach ein neues Bookmark hinzuf&uuml;gen. Nur die Felder ausf&uuml;llen, Kategorie ausw&auml;len, fertig!</p>
  </div>

  <div class="popup-form">

    <form action="addBookmark.php?done=true" method="post">
      <input type="hidden" name="userid" id="userid" value="<?php echo $userdata['ID']; ?>">
      <div class="container">

        <div class="row">
          <div class="col left">
            <label for="name">Name:</label>
          </div>

          <div class="col right">
            <input autocomplete="off" type="text" name="name" id="name" value="<?php echo $title; ?>"/>
          </div>
        </div>


        <div class="row">
          <div class="col left">
            <label for="url">URL:</label>
          </div>

          <div class="col right">
            <input autocomplete="off" type="text" name="url" id="url" value="<?php echo $url; ?>"/>
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
                echo '<option value="' . $cat['ID'] . '">' . $cat['NAME'] . '</option>';
              }; ?>
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
