<?php

include('functions.php');
include('static/Konst.php');
require('Logger.php');

session_start();

if ($_SESSION['login'] != OKAY) {
  Logger::trace('main(): Login falsch! Weiterleitung zu Header-Location->' . PROJECT_STARTPAGE);
  header("Location: " . PROJECT_STARTPAGE);
}

$userdata = $_SESSION['userdata'];

Logger::trace("init(): Login: ID=". $userdata['ID'] . " NANE=". $userdata['NAME'] . " EMAIL=" . $userdata['EMAIL'] . " PASS=" . $userdata['PASS'] . " VERIFIED=" . $userdata['VERIFIED'] . " CREATED=" . $userdata['CREATED'] . " LASTLOGIN=" . $userdata['LASTLOGIN']);

if (isset($_POST["submit"])) {
  $categoryid = $_POST["categoryid"];
  Logger::trace("main(): POST->categoryid: " . $categoryid);
}

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
  <script>
      $(document).ready(function(){
          $("#btnClick").click(function(){
              alert("Hello! I am an alert box!!");
          });
      });

      setTimeout(function() {
          $('#alert').fadeOut('fast');
      }, <?php echo ALERT_TIMEOUT;?>);
  </script>
</head>
<body>


<?php
if (isset($_POST['name']) && isset($_POST['url']) && isset($_POST['tags']) && isset($_POST['catid'])&& isset($_POST['userid'])) {

  Logger::trace("main(): Bookmark hinzufuegen geklickt");
  Logger::trace("main(): NAME=" . $_POST['name'] . ' URL=' . $_POST['url'] . ' TAGS=' . $_POST['tags'] . ' CATID=' . $_POST['catid'] . ' USERID=' . $_POST['userid']);

  $newBookmarkName = $_POST['name'];
  $newBookmarkUrl = $_POST['url'];
  $newBookmarkTags = $_POST['tags'];
  $newBookmarkCatId = $_POST['catid'];
  $newBookmarkUserId = $_POST['userid'];

  $addResult = addNewBookmark($newBookmarkName, $newBookmarkUrl, $newBookmarkTags, $newBookmarkCatId, $newBookmarkUserId);

  if ($addResult) {
    echo '<div id="alert" class="alert alert-info" role="alert">Bookmark hinzugef??gt!</div>';
  }
}

if (isset($_POST['delete_bookmark_bookmarkid']) && isset($_POST['delete_bookmark_catid']) && isset($_POST['delete_bookmark_userid'])) {
  $delete_bookmark_bookmarkid = $_POST['delete_bookmark_bookmarkid'];
  $delete_bookmark_catid = $_POST['delete_bookmark_catid'];
  $delete_bookmark_userid = $_POST['delete_bookmark_userid'];

  Logger::trace("main(): Bookmark loeschen geklickt");
  Logger::trace("main(): BOOKMARKID=" . $_POST['delete_bookmark_bookmarkid'] . ' CATID=' . $_POST['delete_bookmark_catid'] . ' USERID=' . $_POST['delete_bookmark_userid']);

  if (deleteBookmark($delete_bookmark_bookmarkid, $delete_bookmark_userid, $delete_bookmark_catid)) {
    echo '<div id="alert" class="alert alert-info" role="alert">Bookmark gel??scht</div>';
  } else {
    echo '<div id="alert" class="alert alert-danger" role="alert">Es ist ein Fehler beim L??schen des Bookmarks aufgetreten!</div>';
  }
}


if (isset($_GET['category']) && isset($_GET['color'])) {
  $categoryname = $_GET['category'];
  $color = $_GET['color'];

  Logger::trace("main(): Kategorie hinzuf??gen geklickt. CATEGORY=" . $categoryname . ' COLOR=' . $color);

  if (validateCategory($categoryname, $userdata['ID']) && validateColor($color)) {
    if (addNewCategory($categoryname, $color, $userdata['ID'])) {
      echo '<div id="alert" class="alert alert-info" role="alert">Kategorie hinzugef??gt!</div>';
    } else {
      echo '<div id="alert" class="alert alert-danger" role="alert">Es ist ein Fehler beim Anlegen der Kategorie aufgetreten!</div>';
    }
  }
}


if (isset($_POST['delete-category-catid']) && isset($_POST['delete-category-userid'])) {
  $catid = $_POST['delete-category-catid'];
  $userid = $_POST['delete-category-userid'];

  if ($catid == "0") {
    echo '<div id="alert" class="alert alert-danger" role="alert">Die Default-Kategorie kann nicht gel??scht werden!</div>';
    return;
  }

  Logger::trace("main(): Kategorie loeschen geklickt. CATEGORYID=" . $catid . ' USERID=' . $userid);

  if (deleteCategory($userid, $catid)) {
    echo '<div id="alert" class="alert alert-info" role="alert">Kategorie gel??scht, Bookmarks verschoben!</div>';
  } else {
    echo '<div id="alert" class="alert alert-danger" role="alert">Es ist ein Fehler beim L??schen der Kategorie aufgetreten!</div>';
  }
}


$basetarget = getParameter(BASETARGET, $userdata['ID']);
?>



<div class="column-left">
  <div class="column-header-left">
    <div class="logo-image">
      <a href="main.php"><img src="images/logo.png" alt="<?php echo PROJECTNAME; ?>" title="<?php echo PROJECTNAME; ?>" width="40"></a>
    </div>
  </div>
  <div class="demo-content icons">
    <ul>
      <li><a href="<?php echo DASHBOARD;?>" title="Bookmarks nach Liste"><i class="fa-solid fa-list"></i></a></li>

      <?php if (getParameterBoolean(SHOW_TAG_TAB, $userdata['ID'])) {
        echo '<li><a href="" title="Bookmarks nach Tags"><i class="fa-solid fa-hashtag"></i></a></li>';
      }?>

      <?php if (getParameterBoolean(SHOW_PROFILE_TAB, $userdata['ID'])) {
        echo '<li><a href="" title="Profil-Informationen"><i class="fa-solid fa-user"></i></a></li>';
      }?>

      <?php if (getParameterBoolean(SHOW_SETTINGS_TAB, $userdata['ID'])) {
        echo '<li><a href="" title="Einstellungen"><i class="fa-solid fa-gears"></i></a></li>';
      }?>

      <?php if (getParameterBoolean(SHOW_UPLOAD_TAB, $userdata['ID'])) {
        echo '<li><button id="uploadBtn" class="fa-button" title="File-Upload Bookmark-Browser-Export"><i class="fa-solid fa-file-arrow-up"></i></button></li>';
      }?>

      <li><a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
    </ul>
  </div>


</div>

<div class="column-middle">
  <div class="column-header-middle">
    <div class="project-name"><?php echo PROJECTNAME; ?></div>
    <div class="project-version"><a href="<?php echo GITHUB_RELEASEINFO;?>" target="_blank" title="Releaseinfo @github"><?php echo PROJECTVERSION; ?></a></div>
  </div>
  <div class="demo-content">
    <div class="category-headline">KATEGORIEN <button id="addCatBtn" class="fa-upl-button" title="Kategorie hinzuf??gen"><i class="fa-solid fa-circle-plus"></i></button></div>
    <ul class="category-list">
      <li>
        <div class="category-entry">
          <div class="category-bullet" style="background-color: red;"></div>
          <div class="category-name"><a href="main.php">Default</a></div>
          <div class="category-number"><?php echo getNumberOfBookmarksById(0, $userdata['ID']);?></div>
        </div>
      </li>

      <?php
      $categories = getCategorieListByUserId($userdata['ID']);

      foreach ($categories as $row) {
        echo '<li>';
        echo '  <div class="category-entry">';
        echo '    <div class="category-bullet" style="background-color: ' . $row['COLOR'] . ';"></div>';
        echo '    <div class="category-name"><a href="main.php?cat=' . $row['ID'] . '">' . $row['NAME'] . '</a></div>';
        echo '    <div class="category-number">' . getNumberOfBookmarksById($row['ID'], $userdata['ID']) . '</div>';
        echo '  </div>';
        echo '</li>';
      }
      ?>
    </ul>
  </div>
</div>

<div class="column-right">
  <div class="column-header-right">
    <?php
    $categoriyName = 'Default';
    $categoryId = 0;

    if (!empty($_GET['cat']) && is_numeric($_GET['cat'])) {
      $categoryId = $_GET['cat'];
      Logger::trace("main(): POST cat=" . $categoryId);
    } else {
      Logger::trace("main(): Keine Kategorie-ID uebergeben, setze Default-Wert: 0");
    }

    if ($categoryId != 0) {
      $name = getCategoryNameById($categoryId, $userdata['ID']);

      if (!empty($name)) {
        $categoriyName = $name;
      } else {
        Logger::trace("main(): Falsche Kategorie-ID, Default wird zurueckgegeben");
      }
    }

    Logger::trace("main(): CategoryName=" . $categoriyName);

    echo '<div class="category-main-title"><i class="fa-solid fa-align-justify"></i> ' . $categoriyName . '</div>';
    ?>

    <form name="delete-category-form" action="main.php" method="post">
      <input type="hidden" id="delete-category-catid" name="delete-category-catid" value="<?php echo $categoryId;?>">
      <input type="hidden" id="delete-category-userid" name="delete-category-userid" value="<?php echo $userdata['ID'];?>">
      <button type="submit" class="delete-category" style="<?php echo !isset($categoryId) || $categoryId == "0" || $categoryId == 0 ? 'display:none;' : '' ;?>" title="INFO: Alle Bookmarks der gel??schten Kategorie werden der Default-Kategorie zugewiesen!"><i class="fa-solid fa-trash-can"></i></button>
    </form>

    <?php if ($categoryId > 0) {
      echo '<form action="main.php?cat=' . $categoryId . '" method="post">';
    } else {
      echo '<form action="main.php" method="post">';
    }?>
    <div class="new-bookmark">
      <input type="hidden" name="catid" id="catid" value="<?php echo $categoryId;?>">
      <input type="hidden" name="userid" id="userid" value="<?php echo $userdata['ID'];?>">
      <div class="new-bookmark-name">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" placeholder="Meine coole Seite"/>
      </div>
      <div class="new-bookmark-url">
        <label for="url">URL:</label>
        <input type="text" name="url" id="url" placeholder="https://google.de"/>
      </div>
      <div class="new-bookmark-tags">
        <label for="tags">Tags:</label>
        <input type="text" name="tags" id="tags" placeholder="suchen,musik"/>
      </div>
      <div class="submitBookmark">
        <button type="submit" id="submitNewBookmark" title="Bookmark hinzuf??gen"><i class="fa-solid fa-circle-plus"></i></button>
      </div>
    </div>
    </form>

    <div class="user-panel">
      <div class="user-icon-box">
        <div class="user-icon"><i class="fa-solid fa-user"></i></div>
        <div class="user-welcome-text">Hallo, <?php echo $userdata['NAME'];?>!</div>
      </div>
    </div>

  </div>
  <div class="category-bookmarks">
    <ul>
    <?php
    $categoryId = 0;

    if (!empty($_GET['cat']) && is_numeric($_GET['cat'])) {
      $categoryId = $_GET['cat'];
      Logger::trace("main(): POST cat=" . $categoryId);
    } else {
      Logger::trace("main(): Keine Kategorie-ID uebergeben, setze Default-Wert: 0");
    }

    $bookmarks = getBookmarksByCategoryId($categoryId, $userdata['ID']);

    if (!empty($bookmarks)) {
      foreach ($bookmarks as $bookmark) {

        $tags = '';

        if (!empty($bookmark['TAGS'])) {
          $bookmarkTags = explode(",", $bookmark['TAGS']);
          foreach ($bookmarkTags as $tag) {
            $tags .= '#' . $tag . ' ';
          }
        }

        echo '<li class="bookmark-link-entry">';
        echo '<a href="' . $bookmark['URL'] . '" target="' . $basetarget . '">';
        echo '  <div class="bookmark-box">';
        echo '    <div class="bookmark-name">';
        echo '      <i class="fa-regular fa-bookmark"></i> ' . $bookmark['NAME'];
        echo '    </div>';
        echo '    <div class="bookmark-url">';
        echo '      <small><i>' . $bookmark['URL'] . '</i></small>';
        echo '    </div>';
        echo '    <div class="bookmark-tags">';
        echo '      <small><i>' . $tags . '</i></small>';
        echo '    </div>';
        echo '  </div>';
        echo '</a>';
        $cat_name = getCategoryNameById($categoryId, $userdata['ID']);
        $cat_link = categoryExists($cat_name, $userdata['ID']) ? '?cat=' . $categoryId : '';
        echo '<form id="delete" action="main.php' . $cat_link . '" method="post">';
        echo '<button class="delete-bookmark" title="Bookmark l??schen">';
        echo '<input type="hidden" name="delete_bookmark_bookmarkid" id="delete_bookmark_bookmarkid" value="' . $bookmark['ID'] . '">';
        echo '<input type="hidden" name="delete_bookmark_catid" id="delete_bookmark_catid" value="' . $categoryId . '">';
        echo '<input type="hidden" name="delete_bookmark_userid" id="delete_bookmark_userid" value="' . $userdata['ID'] . '">';
        echo '<i class="fa-solid fa-trash-can"></i>';
        echo '</button>';
        echo '</form>';
        echo '</li>';

      }
    } else {
      echo '<li><i class="fa-solid fa-not-equal"></i> Keine Bookmarks in dieser Kategorie vorhanden.<br/><small>Du kannst ??ber die Eingabefelder oben einfach ein neues Bookmark zu dieser Kategorie hinzuf??gen.</small></li>';
    }
    ?>
    </ul>

    <?php include('includes/footer.inc.php');?>
  </div>
</div>

  <?php
  if ("1" === getParameter(SHOW_UPLOAD_TAB, $userdata['ID'])) {
    include('includes/uploadwindow.php');
  }?>

  <?php include('includes/addcategory.php');?>

</body>
</html>
