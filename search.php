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
$userID = $userdata['ID'];
?>

<script>
  function deleteSearchData() {
      document.getElementById('search').value = "";
      dontshowResultBox();
  }

  function dontshowResultBox() {
      document.getElementById('search-results').style.display = 'none';
  }

  function showResultBox() {
      document.getElementById('search-results').style.display = 'block';
  }
</script>

<?php
if (isset($_POST['query'])) {
  $query = $_POST['query'];

  if ($query != '' && $query != ' ') {
    $pdo = new PDO('sqlite:db/bookmarkservice.db');
    $stmt = $pdo->prepare("SELECT * FROM BOOKMARK WHERE USERID=:userID AND (NAME LIKE :query OR TAGS LIKE :query OR URL LIKE :query)");
    $stmt->bindValue(':userID', $userID);
    $stmt->bindValue(':query', '%' . $query . '%');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($results) > 0) {
      echo '<script>showResultBox();</script>';

      echo '<div class="search-result-box">';

      foreach ($results as $result) {
        $title = trim($result['NAME']);

        if (empty($title)) {
          $title = $result['URL'];
        }

        // Kategorie nachselektieren!
        $categoryName = getCategoryNameById($result['CATEGORYID'], $userID);

        if (empty($categoryName)) {
          $categoryName = 'Default';
        }

        echo '<div class="search-entry"><a href="' . $result['URL'] . '" target="_blank" onclick="deleteSearchData();dontshowResultBox();">' . $title . ' <small><em>(' . $categoryName . ')</em></small></a></div>';
      }

      echo '</div>';
    } else {
      echo '<script>dontshowResultBox();</script>';
    }
  } else {
    echo '<script>dontshowResultBox();</script>';
  }
}
