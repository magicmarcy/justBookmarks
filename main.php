<?php
include_once('functions.php');
include_once('static/Konst.php');
require_once('Logger.php');

session_start();

if ($_SESSION[SESSION_LOGIN] != OKAY) {
  Logger::trace('Login falsch! Weiterleitung zu Header-Location-> ' . PROJECT_STARTPAGE);
  header("Location: " . PROJECT_STARTPAGE);
}

deleteAddCookie();

$userdata = $_SESSION[SESSION_USERDATA];

Logger::trace(<<<EOD
Login: ID=${userdata[FIELD_ID]} NAME=${userdata[FIELD_NAME]} EMAIL=${userdata[FIELD_EMAIL]} PASS=${userdata[FIELD_PASS]} VERIFIED=${userdata[FIELD_VERIFIED]} CREATED=${userdata[FIELD_CREATED]} LASTLOGIN=${userdata[FIELD_LASTLOGIN]}
EOD);

if (isset($_POST[POST_SUBMIT])) {
  $categoryid = $_POST[POST_CATEGORYID];
  Logger::trace("POST->categoryid: " . $categoryid);
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title><?=PROJECTSHORTDESC;?></title>
  <meta name="description" content="<?=PROJECTSHORTDESC;?>">
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="<?=STYLE_DEFAULT_CSS;?>">
  <link rel="stylesheet" href="<?=STYLE_BOOTSTRAPMIN;?>">
  <link rel="stylesheet" href="<?=STYLE_FONTAWESOMEMIN;?>">
  <link rel="stylesheet" href="<?=STYLE_FONTAWESOMEALLMIN;?>">
  <script src="<?=JS_JQUERY;?>"></script>
  <script src="<?=JS_DEFAULT;?>"></script>
  <script>
      // Beim Laden der Seite
      window.addEventListener("DOMContentLoaded", function() {
          // Abrufen der gespeicherten Scrollposition aus dem Browser-Speicher
          let scrollPosition = localStorage.getItem("scrollPosition");

          // Überprüfen, ob eine gespeicherte Scrollposition vorhanden ist
          if (scrollPosition !== null) {
              // Wiederherstellen der Scrollposition des linken DIVs
              document.getElementById("content-left").scrollTop = scrollPosition;

              // Löschen der gespeicherten Scrollposition aus dem Browser-Speicher
              localStorage.removeItem("scrollPosition");
          }
      });

      setTimeout(function() {
          let alert = $('#alert');
          if (alert) {
              alert.fadeOut('fast');
          }
      }, <?=ALERT_TIMEOUT;?>);
  </script>
</head>
<body>
<div id="overlay" onclick="hideOverlay();"></div>

<?php
if (isset($_POST[POST_NAME]) && isset($_POST[POST_URL]) && isset($_POST[POST_TAGS]) && isset($_POST[POST_CATID])&& isset($_POST[POST_USERID])) {

  Logger::trace("Bookmark hinzufuegen geklickt");
  Logger::trace('NAME=' . $_POST[POST_NAME] . ' URL=' . $_POST[POST_URL] . ' TAGS=' . $_POST[POST_TAGS] . ' CATID=' . $_POST[POST_CATID] . ' USERID=' . $_POST[POST_USERID]);

  $newBookmarkName = $_POST[POST_NAME];
  $newBookmarkUrl = $_POST[POST_URL];
  $newBookmarkTags = $_POST[POST_TAGS];
  $newBookmarkCatId = $_POST[POST_CATID];
  $newBookmarkUserId = $_POST[POST_USERID];

  $addResult = addNewBookmark($newBookmarkName, $newBookmarkUrl, $newBookmarkTags, $newBookmarkCatId, $newBookmarkUserId);

  if ($addResult) {
    showInfo(ADD_BOOKMARK_SUCCESS);
  }
}

if (isset($_POST[POST_DELETE_BOOKMARK_BOOKMARK_ID]) && isset($_POST[POST_DELETE_BOOKMARK_CAT_ID]) && isset($_POST[POST_DELETE_BOOKMARK_USER_ID])) {
  $delete_bookmark_bookmarkid = $_POST[POST_DELETE_BOOKMARK_BOOKMARK_ID];
  $delete_bookmark_catid = $_POST[POST_DELETE_BOOKMARK_CAT_ID];
  $delete_bookmark_userid = $_POST[POST_DELETE_BOOKMARK_USER_ID];

  Logger::trace("main(): Bookmark loeschen geklickt");
  Logger::trace("main(): BOOKMARKID=" . $_POST[POST_DELETE_BOOKMARK_BOOKMARK_ID] . ' CATID=' . $_POST[POST_DELETE_BOOKMARK_CAT_ID] . ' USERID=' . $_POST[POST_DELETE_BOOKMARK_USER_ID]);

  if (deleteBookmark($delete_bookmark_bookmarkid, $delete_bookmark_userid, $delete_bookmark_catid)) {
    showInfo(DELETE_BOOKMARK_SUCCESS);
  } else {
    showError(DELETE_BOOKMARK_ERROR);
  }
}

if (isset($_GET[GET_CATEGORY]) && isset($_GET[GET_COLOR])) {
  $categoryname = $_GET[GET_CATEGORY];
  $parentcategoryid = $_GET[GET_PARENT_CATEGORY_ID];
  $color = $_GET[GET_COLOR];

  Logger::trace("Kategorie hinzufügen geklickt. CATEGORY=" . $categoryname . ' COLOR=' . $color . ' PARENTID=' . $parentcategoryid);

  if (validateCategory($categoryname, $userdata[FIELD_ID]) && validateColor($color)) {
    if (addNewCategory($categoryname, $color, $userdata[FIELD_ID], $parentcategoryid)) {
      showInfo(ADD_CATEGORY_SUCCESS);
    } else {
      showError(ADD_CATEGORY_ERROR);
    }
  }
}

if (isset($_POST[POST_DELETE_CAT_CAT_ID]) && isset($_POST[POST_DELETE_CAT_USER_ID])) {
  $catid = $_POST[POST_DELETE_CAT_CAT_ID];
  $userid = $_POST[POST_DELETE_CAT_USER_ID];

  if ($catid == "0") {
    showError(DELETE_DEFAULT_ERROR);
    return;
  }

  Logger::trace("Kategorie loeschen geklickt. CATEGORYID=" . $catid . ' USERID=' . $userid);

  if (deleteCategory($userid, $catid)) {
    showInfo(DELETE_CATEGORY_SUCCEDD);
  } else {
    showError(DELETE_CATEGORY_ERROR);
  }
}

$basetarget = getParameter(BASETARGET, $userdata[FIELD_ID]);
?>

<div class="column-left">
  <div class="column-header-left">
    <div class="logo-image">
      <a href="<?=DASHBOARD;?>"><img src="images/logo.png" alt="<?=PROJECTNAME;?>" title="<?=PROJECTNAME;?>" width="40"></a>
    </div>
  </div>
  <div class="demo-content icons">
    <ul>
      <li><a href="<?=DASHBOARD;?>" title="Bookmarks nach Liste"><i class="fa-solid fa-list"></i></a></li>

      <?php if (getParameterBoolean(SHOW_TAG_TAB, $userdata[FIELD_ID])) {
        echo '<li><a href="" title="Bookmarks nach Tags"><i class="fa-solid fa-hashtag"></i></a></li>';
      }?>

      <?php if (getParameterBoolean(SHOW_PROFILE_TAB, $userdata[FIELD_ID])) {
        echo '<li><a href="" title="Profil-Informationen"><i class="fa-solid fa-user"></i></a></li>';
      }?>

      <?php if (getParameterBoolean(SHOW_SETTINGS_TAB, $userdata[FIELD_ID])) {
        echo '<li><a href="" title="Einstellungen"><i class="fa-solid fa-gears"></i></a></li>';
      }?>

      <?php if (getParameterBoolean(SHOW_UPLOAD_TAB, $userdata[FIELD_ID])) {
        echo '<li><button id="uploadBtn" class="fa-button" title="File-Upload Bookmark-Browser-Export"><i class="fa-solid fa-file-arrow-up"></i></button></li>';
      }?>

      <?php if (getParameterBoolean(SHOW_BOOKMARKLET_TAB, $userdata[FIELD_ID])) {
        echo '<li><button onclick="showBookmarkletModal()" id="bookmarkletBtn" class="fa-button" title="NEU: justBookmarks Bookmarklet">
              <i class="fa-solid fa-book-medical"></i></button></li>';
      }?>

      <li><a href="logout.php" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
    </ul>
  </div>


</div>

<div class="column-middle">
  <div class="column-header-middle">
    <div class="project-name"><?=PROJECTNAME;?></div>
    <div class="project-version"><a href="<?=GITHUB_RELEASEINFO;?>" target="_blank" title="Releaseinfo @github"><?=PROJECTVERSION;?></a></div>
  </div>
  <div id="content-left" class="demo-content">
    <div class="category-headline">KATEGORIEN <button id="addCatBtn" class="fa-upl-button" title="Kategorie hinzufügen"><i class="fa-solid fa-circle-plus"></i></button></div>
    <ul class="category-list">
      <li>
        <div class="category-entry">
          <div class="category-bullet" style="background-color: red;"></div>
          <div class="category-name"><a href="<?=DASHBOARD;?>" onclick="saveScroll();"><?=DEFAULT_CAT_NAME;?></a></div>
          <div class="category-number"><?=getNumberOfBookmarksById(0, $userdata[FIELD_ID]);?></div>
        </div>
      </li>

      <?php
      $categories = getCategorieListByUserId($userdata[FIELD_ID], true);

      foreach ($categories as $row) {
        if ($row[FIELD_PARENT] == '0') {
          echo '<li>';
          echo '  <div class="category-entry">';
          echo '    <div class="category-bullet" style="background-color: ' . $row[FIELD_COLOR] . ';"></div>';
          echo '    <div class="category-name cat-short one"><a href="' . DASHBOARD. CAT_URL_PREFIX . $row[FIELD_ID] . '" onclick="saveScroll();">' . $row[FIELD_NAME] . '</a></div>';
          echo '    <div class="category-number">' . getNumberOfBookmarksById($row[FIELD_ID], $userdata[FIELD_ID]) . '</div>';
          echo '  </div>';
          echo '</li>';

          $subcategories = getSubCategorieListByUserId($userdata[FIELD_ID], $row[FIELD_ID]);

          foreach ($subcategories as $subrow) {
            echo '<li style="padding-left:15px;">';
            echo '  <div class="subcategory-entry">';
            echo '    <div class="category-name cat-short two"><i style="color: ' . $subrow[FIELD_COLOR] . '; padding-right:5px;" class="fa-solid fa-square"></i> <a href="' . DASHBOARD . CAT_URL_PREFIX . $subrow[FIELD_ID] . '" onclick="saveScroll();">' . $subrow[FIELD_NAME] . '</a></div>';
            echo '    <div class="category-number">' . getNumberOfBookmarksById($subrow[FIELD_ID], $userdata[FIELD_ID]) . '</div>';
            echo '  </div>';
            echo '</li>';

            $subsubcategories = getSubCategorieListByUserId($userdata[FIELD_ID], $subrow[FIELD_ID]);

            foreach ($subsubcategories as $subsubrow) {
              echo '<li style="padding-left:30px;">';
              echo '  <div class="subsubcategory-entry">';
              echo '    <div class="category-name cat-short three"><i style="color: ' . $subsubrow[FIELD_COLOR] . '; padding-right:5px;" class="fa-solid fa-square"></i> <a href="' . DASHBOARD . CAT_URL_PREFIX . $subsubrow[FIELD_ID] . '" onclick="saveScroll();">' . $subsubrow[FIELD_NAME] . '</a></div>';
              echo '    <div class="category-number">' . getNumberOfBookmarksById($subsubrow[FIELD_ID], $userdata[FIELD_ID]) . '</div>';
              echo '  </div>';
              echo '</li>';
            }
          }
        }
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

    if (!empty($_GET[GET_CAT]) && is_numeric($_GET[GET_CAT])) {
      $categoryId = $_GET[GET_CAT];
      Logger::trace("GET cat=" . $categoryId);
    } else {
      Logger::trace("Keine Kategorie-ID uebergeben, setze Default-Wert: 0");
    }

    if ($categoryId != 0) {
      $name = getCategoryNameById($categoryId, $userdata[FIELD_ID]);

      if (!empty($name)) {
        $categoriyName = $name;
      } else {
        Logger::trace("Falsche Kategorie-ID, Default wird zurueckgegeben");
      }
    }

    Logger::trace("CategoryName=" . $categoriyName);

    echo '<div class="category-main-title"><i class="fa-solid fa-align-justify"></i> ' . $categoriyName . '</div>';
    ?>

    <form name="delete-category-form" action="<?=DASHBOARD;?>" method="post">
      <input type="hidden" id="delete-category-catid" name="delete-category-catid" value="<?=$categoryId;?>">
      <input type="hidden" id="delete-category-userid" name="delete-category-userid" value="<?=$userdata[FIELD_ID];?>">
      <button type="submit" class="delete-category" style="<?=!isset($categoryId) || $categoryId == "0" || $categoryId == 0 ? 'display:none;' : '' ;?>" title="INFO: Alle Bookmarks der gelöschten Kategorie werden der Default-Kategorie zugewiesen!"><i class="fa-solid fa-trash-can"></i></button>
    </form>

    <input type="text" onkeydown="focusFirstEntry(event, this);closeAndDeleteSearchData(event);" id="search" placeholder="Alle Bookmarks durchsuchen [ALT + W] ...">
    <button class="deleteSearchBoxIcon" onclick="deleteSearchData();" title="Eingabe löschen"><i class="fa-solid fa-trash-can"></i></button>
    <div id="search-results" style="display:none;" onkeydown="fucusNextEntry(event);"></div>

    <div class="user-panel">
      <div class="user-icon-box">
        <div class="user-icon"><i class="fa-solid fa-user"></i></div>
        <div class="user-welcome-text">Hallo, <?=$userdata[FIELD_NAME];?>!</div>
      </div>
    </div>

  </div>
  <div class="category-bookmarks">
    <ul>
    <?php
    $categoryId = 0;

    if (!empty($_GET[GET_CAT]) && is_numeric($_GET[GET_CAT])) {
      $categoryId = $_GET[GET_CAT];
      Logger::trace("GET cat=" . $categoryId);
    } else {
      Logger::trace("Keine Kategorie-ID uebergeben, setze Default-Wert: 0");
    }

    $bookmarks = getBookmarksByCategoryId($categoryId, $userdata[FIELD_ID]);

    if (!empty($bookmarks)) {
      foreach ($bookmarks as $bookmark) {

        $tags = '';

        if (!empty($bookmark[FIELD_TAGS])) {
          $bookmarkTags = explode(",", $bookmark[FIELD_TAGS]);
          foreach ($bookmarkTags as $tag) {
            $tags .= '#' . $tag . ' ';
          }
        }

        echo '<li class="bookmark-link-entry">';
        echo '<a href="' . $bookmark[FIELD_URL] . '" target="' . $basetarget . '">';
        echo '  <div class="bookmark-box">';
        echo '    <div class="bookmark-name truncated">';
        getFaviconFromUrl($bookmark[FIELD_URL], $userdata[FIELD_ID]);
        echo '      ' . (empty(trim($bookmark[FIELD_NAME])) ? $bookmark[FIELD_URL] : $bookmark[FIELD_NAME]);
        echo '    </div>';
        echo '    <div class="bookmark-url">';
        echo '      <small><i>' . $bookmark[FIELD_URL] . '</i></small>';
        echo '    </div>';
        echo '    <div class="bookmark-tags">';
        echo '      <small><i>' . $tags . '</i></small>';
        echo '    </div>';
        echo '  </div>';
        echo '</a>';
        $cat_name = getCategoryNameById($categoryId, $userdata[FIELD_ID]);
        $cat_link = categoryExists($cat_name, $userdata[FIELD_ID]) ? CAT_URL_PREFIX . $categoryId : '';
        echo '<form id="delete" action="'. DASHBOARD . $cat_link . '" method="post">';
        echo '<button class="delete-bookmark" title="Bookmark löschen">';
        echo '<input type="hidden" name="delete_bookmark_bookmarkid" id="delete_bookmark_bookmarkid" value="' . $bookmark[FIELD_ID] . '">';
        echo '<input type="hidden" name="delete_bookmark_catid" id="delete_bookmark_catid" value="' . $categoryId . '">';
        echo '<input type="hidden" name="delete_bookmark_userid" id="delete_bookmark_userid" value="' . $userdata[FIELD_ID] . '">';
        echo '<i class="fa-solid fa-trash-can"></i>';
        echo '</button>';
        echo '</form>';
        echo '</li>';

      }
    } else {
      echo '<li class="no-items"><i class="fa-solid fa-not-equal"></i> ' . NO_BOOKMARKS_IN_CATEGORY_HEADLINE . '<br/>
            <small>Du hast in dieser Kategorie noch keine Bookmarks gespeichert.<br/>Klicke
            <a style="cursor: pointer" onclick="showAddBookmarkModal();"><b>hier</b></a>
            um ein neues Bookmark dieser Katergorie hinzuzuf&uuml;gen</small></li>';
    }
    ?>
    </ul>

    <div class="upload-overlay" title="Bookmark hinzufügen">
      <div class="add-bookmark-icon">
        <a onclick="showAddBookmarkModal();deleteSearchData();"><img src="images/add_bookmark.png" alt="Add Bookmark" class="add-bookmark-icon"></a>
      </div>
    </div>

    <?php include_once('includes/footer.inc.php');?>
  </div>
</div>

  <?php
  if ("1" === getParameter(SHOW_UPLOAD_TAB, $userdata[FIELD_ID])) {
    include_once('includes/uploadwindow.php');
  }

  if ("1" === getParameter(SHOW_BOOKMARKLET_TAB, $userdata[FIELD_ID])) {
    include_once('includes/bookmarkletwindow.php');
  }
  ?>

  <?php include_once('includes/addcategory.php');?>

<div id="bookmark-modal">
  <div class="modal-header">
    <div class="modal-headline"><?=BOOKMARK_HINZUFUEGEN;?></div>
    <div class="modal-close" title="Schließen"><a onclick="hideAddBookmarkModal();">X</a></div>
  </div>
  <div class="modal-content">

    <?php if ($categoryId > 0) {
      echo '<form action="' . DASHBOARD . CAT_URL_PREFIX . $categoryId . '" method="post">';
    } else {
      echo '<form action="' . DASHBOARD . '" method="post">';
    }?>

    <input type="hidden" name="catid" id="catid" value="<?=$categoryId;?>">
    <input type="hidden" name="userid" id="userid" value="<?=$userdata[FIELD_ID];?>">
    <div class="container">
      <div class="row">
        <div class="col">
          <label for="name"><?=NAME;?>:</label>
        </div>
        <div class="col">
          <input autocomplete="off" type="text" name="name" id="name" onkeydown="validateNewBookmark(this.id);" placeholder="Meine coole Seite"/>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <label for="url"><?=URL;?>:</label>
        </div>
        <div class="col">
          <input autocomplete="off" type="text" name="url" id="url" onkeydown="validateNewBookmark(this.id);" placeholder="https://google.de"/>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <label for="tags"><?=TAGS;?>>:</label>
        </div>
        <div class="col">
          <input type="text" name="tags" id="tags" placeholder="suchen,musik"/>
        </div>
      </div>
      <div class="submit-cancel-panel">
        <span class="btn btn-cancel" id="cancel" onclick="hideAddBookmarkModal();" title="Abbrechen"><?=ABBRECHEN;?></span>
        <button class="btn btn-submit" disabled="disabled" type="submit" id="submitNewBookmark" title="Bookmark hinzufügen"><?=HINZUFUEGEN;?></button>
      </div>
    </div>
    <?='</form>';?>

  </div>
</div>

</body>
</html>
