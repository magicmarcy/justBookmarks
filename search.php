<?php
include_once('functions.php');
include_once('static/Konst.php');
require_once('Logger.php');

session_start();

if ($_SESSION[SESSION_LOGIN] != OKAY) {
  Logger::trace('Login falsch! Weiterleitung zu Header-Location->' . PROJECT_STARTPAGE);
  header("Location: " . PROJECT_STARTPAGE);
}

$userdata = $_SESSION[SESSION_USERDATA];
$userID = $userdata[FIELD_ID];

if (isset($_POST[POST_QUERY])) {
  $query = $_POST[POST_QUERY];

  if ($query != '' && $query != ' ') {
    $pdo = new PDO(PROJECT_DATABASE);
    $stmt = $pdo->prepare("SELECT * FROM BOOKMARK WHERE USERID = :userid AND (NAME LIKE :query OR TAGS LIKE :query OR URL LIKE :query)");
    $stmt->bindValue(PARAM_USERID, $userID);
    $stmt->bindValue(PARAM_QUERY, '%' . $query . '%');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
      echo '<script>showResultBox();</script>';

      echo '<div class="search-result-box">';

      foreach ($results as $result) {
        $title = trim($result[FIELD_NAME]);

        if (empty($title)) {
          $title = $result[FIELD_URL];
        }

        // Kategorie nachselektieren!
        $categoryName = getCategoryNameById($result[FIELD_CATEGORYID], $userID);

        if (empty($categoryName)) {
          $categoryName = 'Default';
        }

        echo '<div class="search-entry"><a href="' . $result[FIELD_URL] . '" target="_blank" onclick="deleteSearchData();dontshowResultBox();">' . $title . ' <small><em>(' . $categoryName . ')</em></small></a></div>';
      }

      echo '</div>';
    } else {
      echo '<script>dontshowResultBox();</script>';
    }
  } else {
    echo '<script>dontshowResultBox();</script>';
  }
}
