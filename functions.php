<?php
/**
 * Liefert alle Kategorien der uebergebenen User-ID, sortiert nach NAME zurueck.
 */
function getCategorieListByUserId($userid, $withsubs): array
{
  Logger::traceEntryData($userid);

  $categories = [];

  if (!isset($userid)) {
    Logger::error('Keine User-ID erhalten!');
    return $categories;
  }

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);

  if ($withsubs) {
    $sql = "SELECT ID, NAME, USERID, COLOR, PARENT FROM CATEGORY WHERE USERID = :id ORDER BY 2";
  } else {
    $sql = "SELECT ID, NAME, USERID, COLOR, PARENT FROM CATEGORY WHERE USERID = :id AND PARENT = 0 ORDER BY 2";
  }

  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_ID, $userid);

  Logger::sql($sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach ($res as $row) {
    $categories[] = ['ID' => $row[FIELD_ID], 'NAME' => $row[FIELD_NAME], 'USERID' => $row[FIELD_USERID], 'COLOR' => $row[FIELD_COLOR], 'PARENT' => $row[FIELD_PARENT]];

    Logger::trace('Datensatz erzeugt: ' . json_encode($row));
  }

  if (empty($categories)) {
    Logger::warn(NO_RECORDS_FOUND);
  } else {
    Logger::trace(formatString(RECORDS_FOUND, [sizeof($categories)]));
  }

  $db = null;

  return Logger::traceExitData($categories);
}

function getSubCategorieListByUserId($userid, $categoryid): array
{
  Logger::traceEntryData('UserId=' . $userid . ' CategoryId=' . $categoryid);

  $categories = [];

  if (!isset($userid)) {
    Logger::error('Keine User-ID erhalten!');
    return $categories;
  }

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, NAME, USERID, COLOR, PARENT FROM CATEGORY WHERE USERID = :id AND PARENT = :parent ORDER BY 2";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_ID, $userid);
  $stmt -> bindParam(PARAM_PARENT, $categoryid);

  Logger::sql($sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach ($res as $row) {
    $categories[] = ['ID' => $row[FIELD_ID], 'NAME' => $row[FIELD_NAME], 'USERID' => $row[FIELD_USERID], 'COLOR' => $row[FIELD_COLOR], 'PARENT' => $row[FIELD_PARENT]];

    Logger::trace('getSubCategorieListByUserId(): Datensatz erzeugt: ID=' . $row[FIELD_ID] . ' NAME=' . $row[FIELD_NAME] . ' USERID=' . $row[FIELD_USERID] . ' COLOR=' . $row[FIELD_COLOR] . ' PARENT=' . $row[FIELD_PARENT]);
  }

  if (empty($categories)) {
    Logger::warn(NO_RECORDS_FOUND);
  } else {
    Logger::trace(formatString(RECORDS_FOUND, [sizeof($categories)]));
  }

  $db = null;

  return Logger::traceExitData($categories);
}

function getNumberOfBookmarksById($id, $userid) :int
{
  Logger::traceEntryData('ID=' . $id, 'USERID=' . $userid);

  $bookmarks = [];

  if (!is_numeric($id) || !isset($userid)) {
    Logger::error('Keine ID erhalten oder ID keine Zahl (ID=' . $id . ')');
    return sizeof($bookmarks);
  }

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, CATEGORYID, USERID, NAME, URL, TAGS FROM BOOKMARK WHERE CATEGORYID = :id AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_ID, $id);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  if (empty($res)) {
    Logger::warn(NO_RECORDS_FOUND);
  } else {
    Logger::trace(formatString(RECORDS_FOUND, [sizeof($res)]));
  }

  $db = null;

  return Logger::traceExitData(sizeof($res));
}

function checkLogin($username, $password): string
{
  Logger::traceEntryData('username: ' . $username, 'password: ' . $password);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, NAME, EMAIL, PASS, VERIFIED, CREATED, LASTLOGIN FROM USER WHERE (NAME = '" . $username . "' OR EMAIL = '" . $username . "') AND LOWER(PASS) = '" . strtolower($password) . "'";
  $results = $db->query($sql);

  Logger::sql($sql);

  $ergebnis[] = array();

  // alle eingelesenen Datensätze ausgeben
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $ergebnis[] = $row;
  }

  $db = null;

  // Da hier auch ein leerer Satz ein Ergebnis ist, muss das Array exakt 2 groß sein, damit der User mit dem Pass gefunden wurde
  if (count($ergebnis) === 2) {
    Logger::trace("User gefunden, Ergebnis in SessionCookie speichern");

    session_start();

    $_SESSION[SESSION_USERDATA] = $ergebnis[1];

    return Logger::traceExitData(OKAY);
  } else {
    Logger::warn("User konnte nicht gefunden werden oder Zugang falsch!");

    return Logger::traceExitData(NOKAY);
  }
}

function getUserNameAndPassFromToken($userid, $token): array
{
  Logger::traceEntryData('USERID: ' . $userid, 'TOKEN: ' . $token);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT NAME, PASS FROM USER WHERE ID = '" . $userid . "' AND TOKEN = '" . hash('sha1', $token) . "'";
  $results = $db->query($sql);

  Logger::sql($sql);

  $ergebnis[] = array();

  // alle eingelesenen Datensätze ausgeben
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $ergebnis[] = $row;
  }

  $db = null;

  // Da hier auch ein leerer Satz ein Ergebnis ist, muss das Array exakt 2 groß sein, damit der User mit dem Pass gefunden wurde
  if (count($ergebnis) === 2) {
    Logger::trace("User gefunden, Ergebnis in SessionCookie speichern");

    return Logger::traceExitData($ergebnis[1]);
  } else {
    Logger::warn("User konnte nicht gefunden werden oder Zugang falsch!");

    return Logger::traceExitData([]);
  }
}

function getCategoryNameById($catid, $userid)
{
  Logger::traceEntryData('CATID=' . $catid, 'USERID=' . $userid);

  $categorieName = '';

  if (!isset($catid) || !isset($userid)) {
    Logger::error('Keine Kategorie-ID erhalten!');
    return $categorieName;
  }

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT NAME FROM CATEGORY WHERE ID = :catid AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $catid);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach ($res as $row) {
    $categorieName = $row[FIELD_NAME];

    Logger::trace('Datensatz erzeugt: NAME=' . $categorieName);
  }

  if (!isset($categorieName)) {
    Logger::warn('Name der Kategorie konnte nicht gefunden werden.');
  } else {
    Logger::trace('Ergebnis erzeugt, gebe Ergebnis zurueck');
  }

  $db = null;

  return Logger::traceExitData($categorieName);
}

function getBookmarksByCategoryId($categoryid, $userid): array
{
  Logger::traceEntryData('CATID=' . $categoryid, 'USERID=' . $userid);

  $bookmarks = [];

  if (!isset($categoryid) || !isset($userid)) {
    Logger::error('Keine Kategorie-ID erhalten!');
    return Logger::traceExitData($bookmarks);
  }

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, CATEGORYID, USERID, NAME, URL, TAGS FROM BOOKMARK WHERE CATEGORYID = :catid AND USERID = :userid ORDER BY ID DESC";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $categoryid);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach ($res as $row) {
    $bookmarks[] = ['ID' => $row[FIELD_ID], 'CATEGORYID' => $row[FIELD_CATEGORYID], 'USERID' => $row[FIELD_USERID], 'NAME' => $row[FIELD_NAME], 'URL' => $row[FIELD_URL], 'TAGS' => $row[FIELD_TAGS]];

    Logger::trace('Datensatz erzeugt: ID=' . $row[FIELD_ID] . ' CATEGORYID=' . $row[FIELD_CATEGORYID] . ' USERID=' . $row[FIELD_USERID] . ' NAME=' . $row[FIELD_NAME] . ' URL=' . $row[FIELD_URL] . ' TAGS=' . $row[FIELD_TAGS]);
  }

  if (empty($bookmarks)) {
    Logger::warn(NO_RECORDS_FOUND);
  } else {
    Logger::trace(formatString(RECORDS_FOUND, [sizeof($bookmarks)]));
  }

  $db = null;

  return Logger::traceExitData($bookmarks);
}

function console($msg): void
{
  echo '<script type="text/javascript">' . 'console.log(' . $msg . ')</script>';
}

/**
 * wenn ein Parameter mit UserID vorliegt, gilt der auch nur fuer den entsprechenden User
 * ist ein Parameter mit der UserID = 0 angelegt, gilt der Parameter fuer ALLE User
 * Ist ein Parameter gar nicht angelegt, gilt er auch nicht ;-)
 *
 * @param $parametername
 * @param $userid
 * @return string
 */
function getParameter($parametername, $userid): string
{
  Logger::traceEntryData('PARAMETERNAME=' . $parametername, 'USERID=' . $userid);

  $param = [];
  $paramValue = '';

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, NAME, VALUE, USERID FROM PARAMETER WHERE NAME = :parametername";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(':parametername', $parametername);

  Logger::sql($sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  $db = null;

  foreach ($res as $row) {
    $param[] = ['ID' => $row[FIELD_ID], 'NAME' => $row[FIELD_NAME], 'VALUE' => $row[FIELD_VALUE], 'USERID' => $row[FIELD_USERID]];

    Logger::trace('Datensatz erzeugt: ID=' . $row[FIELD_ID] . ' NAME=' . $row[FIELD_NAME] . ' VALUE=' . $row[FIELD_VALUE] . ' USERID=' . $row[FIELD_USERID]);
  }

  if (empty($param)) {
    Logger::warn(NO_RECORDS_FOUND);
  }

  foreach ($param as $parameter) {
    if ($parameter[FIELD_USERID] == $userid) {
      return $parameter[FIELD_VALUE];
    }
  }

  if (empty($paramValue)) {
    foreach ($param as $parameter) {
      if ($parameter[FIELD_USERID] == NO_USER) {
        $paramValue = $parameter[FIELD_VALUE];
        break;
      }
    }
  }

  return Logger::traceExitData($paramValue);
}

function getParameterBoolean($parametername, $userid): bool
{
  Logger::traceEntryData("PARAMETERNAME=" . $parametername, "USERID=" . $userid);

  $result = false;
  $parameterValue = getParameter($parametername, $userid);

  Logger::trace("ParameterValueResult from DB=" . $parameterValue);

  if (isset($parameterValue) && !empty($parameterValue) && $parameterValue == '1') {
    $result = true;
  }

  return Logger::traceExitData($result);
}

function hasForbiddenCharsInCategoryname(string $category): bool
{
  $paramForbiddenChars = getParameter(PARAM_FORBIDDEN_CHARS, NO_USER);

  Logger::trace("PARAMETER PARAM_FORBIDDEN_CHARS=" . $paramForbiddenChars);

  $chars = explode(HASH, $paramForbiddenChars);

  if (isset($chars)) {
    foreach ($chars as $char) {
      Logger::trace("Pruefe auf Vorkommen von: " . $char);

      if (isset($char) && !empty($char) && str_contains($category, $char)) {
        Logger::trace("Verbotenes Zeichen <" . $char . "> in <" . $category . "> gefunden");
        return Logger::traceExitData(true);
      }
    }
  }

  return Logger::traceExitData(false);
}

function categoryExists($categoryName, $userid): bool
{
  Logger::traceEntryData('CATEGORY=' . $categoryName, 'USERID=' . $userid);

  $categoryExists = false;

  if (!isset($categoryName) || !isset($userid)) {
    Logger::error('Keine Kategorie oder UserId erhalten!');
    return Logger::traceExitData($categoryExists);
  }

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, NAME, USERID, COLOR FROM CATEGORY WHERE LOWER(NAME) = :catName AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $catInLowerCase = strtolower($categoryName);
  $stmt -> bindParam(PARAM_CATNAME, $catInLowerCase);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  if (!empty($res)) {
    $categoryExists = true;
  }

  $db = null;

  return Logger::traceExitData($categoryExists);
}


function validateCategory($category, $userid): bool
{
  Logger::traceEntryData("CATEGORY=" . $category, 'USERID=' . $userid);

  if (trim($category) == '') {
    Logger::trace("Kategorie darf nicht leer sein!");
    showError('Du musst einen Kategorienamen angeben!');
    return Logger::traceExitData(false);

  } elseif (hasForbiddenCharsInCategoryname($category)) {
    Logger::trace("Verbotene Zeichen enthalten");
    showError('Der Kategoriename darf keine Sonderzeichen enthalten!');
    return Logger::traceExitData(false);

  } elseif (strlen($category) > CAT_MAX_CHARS) {
    Logger::trace("Kategoriename ist zu lang! +++ Achtung! +++ Vermutlich Request-Manipulation!!!!");
    showError('Der Kategoriename darf nur ' . CAT_MAX_CHARS . ' Zeichen enthalten!');
    return Logger::traceExitData(false);

  } elseif (strtolower($category) == 'default') {
    Logger::trace("Kategorie darf nicht default heissen!");
    showError('Du kannst keine weitere Kategorie mit dem Namen "Default" anlegen!');
    return Logger::traceExitData(false);

  } elseif (categoryExists($category, $userid)) {
    Logger::trace("Kategorie existiert bereits");
    showError('Kategorie existiert bereits!');
    return Logger::traceExitData(false);

  } else {
    Logger::trace("Validierung durchgefuehrt, Kategorie existiert noch nicht");
  }

  return Logger::traceExitData(true);
}

function validateColor($color): bool
{
  if (!str_starts_with($color, HASH)) {
    return Logger::traceExitData(false);
  }

  if (strlen($color) !== 7) {
    return Logger::traceExitData(false);
  }

  return Logger::traceExitData(true);
}

function addNewCategory($categoryname, $color, $userid, $parentcategoryid):bool
{
  Logger::traceEntryData('CATEGORYNAME=' . $categoryname, 'COLOR=' . $color, 'USERID=' . $userid, 'PARENTID=' . $parentcategoryid);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "INSERT INTO CATEGORY (NAME, USERID, COLOR, PARENT) VALUES (:catName, :userid, :color, :parent)";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATNAME, $categoryname);
  $stmt -> bindParam(PARAM_USERID, $userid);
  $stmt -> bindParam(PARAM_COLOR, $color);
  $stmt -> bindParam(PARAM_PARENT, $parentcategoryid);

  Logger::sql($sql);

  $stmt -> execute();

  $db = null;

  return Logger::traceExitData(true);
}

/**
 * Prueft, ob der uebergebene Ausdruck nur aus Zahlen und Buchstaben besteht. Ein Vorkommen jedes anderen Zeichens liefert ein false
 * zurueck. Das gilt auch fuer Leerzeichen!
 *
 * @param $value    String: Der zu pruefende String
 * @return bool     true=okay, false=falsch
 */
function containsForbiddenChars(string $value): bool
{
  return Logger::traceExitData(!ctype_alnum($value));
}


/**
 * Prueft eine Registrierung und validiert dazu zunaechst alle Eingaben (inkl. Fehlermeldungshandling)
 */
function checkRegistrationInfos(string $username, string $email, string $passwort, string $passwortbest): bool
{

  $validationResult = validateRegistration($username, $email, $passwort, $passwortbest);

  if ($validationResult->getValidationType() == ValidationType::ERROR) {
    Logger::warn('ERROR: ' . $validationResult->getValidationMessage());
    echo '<div id="alert" class="alert alert-danger" style="position:fixed; top: 0;right:0;width:100vW;text-align:center;" role="alert">' . $validationResult->getValidationMessage() . '</div>';
    return Logger::traceExitData(false);

  } elseif ($validationResult->getValidationType() == ValidationType::SUCCESS) {
    Logger::trace('SUCCESS: Validierung erfolgreich durchlaufen, fuehre Registrierung durch');

    return addNewUser($username, $email, $passwort, $passwortbest);
  }

  return Logger::traceExitData(true);
}

function addNewUser(string $username, string $email, string $password, string $passwordbest): bool
{
  Logger::traceEntryData('USERNAME=' . $username, 'EMAIL=' . $email, 'PASSWORD=' . $password, 'PASSWORDBEST=' . $passwordbest);

  $lowerEmail = strtolower($email);
  $passSHA = hash('sha1', $password);
  $today = date(DATE_PATTERN_YMD);
  $time = date(TIME_PATTERN_HM);

  $created = $today . ' ' . $time;

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "INSERT INTO USER (NAME, EMAIL, PASS, VERIFIED, CREATED) VALUES (:username, :email, :pass, '1', :created)";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_USERNAME, $username);
  $stmt -> bindParam(PARAM_EMAIL, $lowerEmail);
  $stmt -> bindParam(PARAM_PASS, $passSHA);
  $stmt -> bindParam(PARAM_CREATED, $created);

  Logger::sql($sql);

  $stmt -> execute();

  $id = $db->lastInsertId();

  $db = null;

  if (!is_numeric($id) || $id <= 0) {
    Logger::error("Fehler beim Anlegen des neuen Users! Bitte Log beachten!");
    return Logger::traceExitData(false);
  }

  Logger::trace("User erfolgreich angelegt! USERID=" . $id);
  return Logger::traceExitData(true);
}

function updateLastLogin(string $userid): bool
{
  Logger::traceEntryData('USERID=' . $userid);

  $today = date(DATE_PATTERN_YMD);
  $time = date(TIME_PATTERN_HM);

  $created = $today . ' ' . $time;

  Logger::trace("Setze LastLogin auf " . $created . ' fuer UserId=' . $userid);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "UPDATE USER SET LASTLOGIN = :lastlogin WHERE ID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_LASTLOGIN, $created);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $result = $stmt -> execute();

  $db = null;

  if (!$result) {
    Logger::error("Fehler beim updaten des aktuellen Logins!");
    return Logger::traceExitData(false);
  }

  Logger::trace("User-Login erfolgreich aktualisiert!");
  return Logger::traceExitData(true);
}

function updateLastLoginWithUsernameAndPass(string $username, string $password): void
{
  $userid = getUserIdFromUsernameAndPass($username, $password);

  $result = updateLastLogin($userid);
}

function getUserIdFromUsernameAndPass(string $username, string $password): int
{
  Logger::traceEntryData('USERNAME=' . $username, 'PASS=' . $password);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, NAME, EMAIL, PASS, VERIFIED, CREATED, LASTLOGIN FROM USER WHERE NAME = '" . $username . "' AND LOWER(PASS) = '" . $password . "'";
  $results = $db->query($sql);

  Logger::sql($sql);

  $ergebnis[] = array();

  // alle eingelesenen Datensätze ausgeben
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $ergebnis[] = ['ID' => $row[FIELD_ID], 'NAME' => $row[FIELD_NAME], 'EMAIL' => $row[FIELD_EMAIL], 'PASS' => $row[FIELD_PASS], 'VERIFIED' => $row[FIELD_VERIFIED], 'CREATED' => $row[FIELD_CREATED], 'LASTLOGIN' => $row[FIELD_LASTLOGIN]];
  }

  $db = null;

  if (count($ergebnis) === 2) {
    $newUserId = $ergebnis[1][FIELD_ID];
    return Logger::traceExitData((int) $newUserId);
  }

  return Logger::traceExitData(0);
}

function addNewBookmark(string $newBookmarkName, string $newBookmarkUrl, string $newBookmarkTags, string $newBookmarkCatId, string $newBookmarkUserId): bool
{
  Logger::traceEntryData('NAME=' . $newBookmarkName, 'URL=' . $newBookmarkUrl, 'TAGS=' . $newBookmarkTags, 'CATID=' . $newBookmarkCatId, 'USERID=' . $newBookmarkUserId);

  $tagsWithoutWhitespaces = str_replace(" ", "", $newBookmarkTags);

  $newPosition = getNextPositionIdOfCategory($newBookmarkCatId, $newBookmarkUserId);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "INSERT INTO BOOKMARK (CATEGORYID, USERID, NAME, URL, TAGS, POSITION) VALUES (:catid, :userid, :name, :url, :tags, :position)";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $newBookmarkCatId);
  $stmt -> bindParam(PARAM_USERID, $newBookmarkUserId);
  $stmt -> bindParam(PARAM_NAME, $newBookmarkName);
  $stmt -> bindParam(PARAM_URL, $newBookmarkUrl);
  $stmt -> bindParam(PARAM_TAGS, $tagsWithoutWhitespaces);
  $stmt -> bindParam(PARAM_POSITION, $newPosition);

  Logger::sql($sql);

  $stmt -> execute();

  $id = $db->lastInsertId();

  $db = null;

  if (!is_numeric($id) || $id <= 0) {
    Logger::error("Fehler beim Anlegen des neuen Users! Bitte Log beachten!");
    return Logger::traceExitData(false);
  }

  Logger::trace("Bookmark erfolgreich angelegt! ID=" . $id);
  return Logger::traceExitData(true);
}

function getNextPositionIdOfCategory($categoryid, $userid)
{
  Logger::traceEntryData('Naechste freie PositionsId ermitteln: CATEGORY=' . $categoryid, 'USERID=' . $userid);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT POSITION FROM BOOKMARK WHERE CATEGORYID = :catid AND USERID = :userid ORDER BY POSITION DESC LIMIT 1";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $categoryid);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();

  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  $db = null;

  $lastPosition = 0;
  if ($res) {
    $lastPosition = $res[0][FIELD_POSITION];
  }

  $newPosition = $lastPosition + 1;

  Logger::trace('getNextPositionIdOfCategory(): Ermittelte PositionsID: ' . $newPosition);

  return Logger::traceExitData($newPosition);
}

function deleteBookmark($bookmarkid, $userid, $catid): bool
{
  Logger::traceEntryData('BOOKMARKID=' . $bookmarkid, 'USERID=' . $userid, 'CATID=' . $catid);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "DELETE FROM BOOKMARK WHERE ID = :bookmarkid AND CATEGORYID = :catid AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_BOOKMARKID, $bookmarkid);
  $stmt -> bindParam(PARAM_USERID, $userid);
  $stmt -> bindParam(PARAM_CATID, $catid);

  Logger::sql($sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  $db = null;

  if ($rowCount != 1) {
    Logger::error("Fehler beim Löschen des Bookmarks! Bitte Log beachten!");
    return Logger::traceExitData(false);
  }

  Logger::trace($rowCount . " Bookmark erfolgreich gelöscht!");
  return Logger::traceExitData(true);
}

function deleteCategory($userid, $catid): bool
{
  Logger::traceEntryData('USERID=' . $userid, 'CATID=' . $catid);

  if (isset($catid) && ($catid == "0" || $catid == 0)) {
    Logger::trace('Kategorie 0 uebergeben! Darf nicht geloescht werden! Breche Verarbeitung ab!');
    return Logger::traceExitData(false);
  }

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "DELETE FROM CATEGORY WHERE ID = :catid AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $catid);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  $db = null;

  if ($rowCount != 1) {
    Logger::error("Fehler beim Löschen des Bookmarks! Bitte Log beachten!");
    return Logger::traceExitData(false);
  }

  $movingResult = moveBookmnarksFromCategoryToDefault($userid, $catid);

  Logger::trace($rowCount . " Kategorie erfolgreich gelöscht!");
  Logger::trace($movingResult . " Bookmarks erfolgreich verschoben!");
  return Logger::traceExitData(true);
}

function moveBookmnarksFromCategoryToDefault($userid, $catid): int
{
  Logger::traceEntryData('USERID=' . $userid, 'CATID=' . $catid);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "UPDATE BOOKMARK SET CATEGORYID = 0 WHERE USERID = :userid AND CATEGORYID = :catid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $catid);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  $db = null;

  if ($rowCount < 0) {
    Logger::error("Fehler beim Verschieben der Bookmarks! Bitte Log beachten!");
    return Logger::traceExitData(0);
  }

  Logger::trace($rowCount . " Bookmark erfolgreich zur Default-Kategorie verschoben!");
  return Logger::traceExitData($rowCount);
}

function processingToken(): void
{
  deleteCookieIfExists();
  createAndSetCookie();
}

function createAndSetCookie(): void
{
  // Da der User hier bereits eingeloggt ist, stehen seine Angaben bereits im SessionCookie
  $userdata = $_SESSION[SESSION_USERDATA];
  $userid = $userdata[FIELD_ID];
  $randomString = generateRandomString();
  $randomStringSha1 = hash('sha1', $randomString);

  $cookieValue = $userid . COOKIE_SEPERATOR . $randomString;

  setcookie("justBookmarks", $cookieValue, time()+COOKIE_DEFAULT_TIME*24*30); // 31 Tage gueltig

  // jetzt Updaten wir das Token - verschluesselt - in der User Tabelle
  updateUserToken($userid, $randomStringSha1);
}

/**
 * Hier schreiben wir die Daten zum hinzufuegen eines Bookmarks temporaer in ein Cookie falls der User sich
 * vor dem Hunzufuegen erst einloggen muss. Wenn wir nach dem Login ein solches Cookie finden koennen wir
 * im Popup direkt wieder zuruecknavigieren und die gespeicherten Daten uebergeben.
 *
 * Wenn der User keine Cookies aktiviert hat, muss er sich eh jedes Mal neu einloggen. Hier muessten wir also
 * lediglich schauen ob der Aufruf aus dem Popup kommt und dann ein abgespecktes Loginformular zur Verfuegung
 * stellen.
 *
 * Das Cookie ist lediglich 60 Sekunden gueltig -> bleibt also diese 60 Sekunden eh nur gueltig, wenn man addBookmark
 * aufruft und sich dann NICHT einlogged
 */
function createAddBookmarkCookie(string $title, string $url): void
{
  setcookie("addBookmarkTitle", $title, time() + 60);
  setcookie("addBookmarkUrl", $url, time() + 60);

  Logger::trace("Login erforderlich, Daten temporaer in Cookie gespeichert (falls moeglich)!");
}

/**
 * Aktualisieren des User-Tokens. Wir muessen hier nichts zurueck bekommen weil es uns egal ist
 * ob das funktioniert hat. Im Zweifel muss sich der User neu einloggen.
 */
function updateUserToken(string $userid, string $token): void
{
  Logger::traceEntryData('USERID=' . $userid, 'TOKEN=' . $token);

  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "UPDATE USER SET TOKEN = :token WHERE ID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_TOKEN, $token);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::sql($sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  $db = null;

  if ($rowCount < 0) {
    Logger::error("Fehler beim Update des User-Token! Bitte Log beachten!");
    return;
  }

  Logger::trace($rowCount . " User-Token erfolgreich aktualisiert!");
}

function generateRandomString(): string
{
  Logger::traceEntry();

  $length = 25;
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }

  return Logger::traceExitData($randomString);
}

/**
 * Ist bereits ein Cookie von hier vorhanden? Dann loeschen wir das erstmal
 */
function deleteCookieIfExists(): void
{
  if (isset($_COOKIE[COOKIE_JUSTBOOKMARKS])) {
    setcookie(COOKIE_JUSTBOOKMARKS, STRING_EMPTY, time()-COOKIE_DEFAULT_TIME);
  }
}

function deleteAddCookie(): void
{
  if (isset($_COOKIE[COOKIE_ADDBOOKMARKTITLE])) {
    setcookie(COOKIE_ADDBOOKMARKTITLE, STRING_EMPTY, time()-COOKIE_DEFAULT_TIME);
  }

  if (isset($_COOKIE[COOKIE_ADDBOOKMARKURL])) {
    setcookie(COOKIE_ADDBOOKMARKURL, STRING_EMPTY, time()-COOKIE_DEFAULT_TIME);
  }
}

function checkAddCookie(): array
{
  Logger::traceEntry();

  if (isset($_COOKIE[COOKIE_ADDBOOKMARKTITLE]) && isset($_COOKIE[COOKIE_ADDBOOKMARKURL])) {
    return Logger::traceExitData([$_COOKIE[COOKIE_ADDBOOKMARKTITLE], $_COOKIE[COOKIE_ADDBOOKMARKURL]]);
  } else {
    return Logger::traceExitData([]);
  }
}

function checkCookie(): array
{
  Logger::traceEntry();

  if (isset($_COOKIE[COOKIE_JUSTBOOKMARKS])) {
    Logger::trace("Cookie gefunden... werte Inhalt aus");

    $wertarray = explode(COOKIE_SEPERATOR, $_COOKIE[COOKIE_JUSTBOOKMARKS]);

    if (sizeof($wertarray) == 2) {
      Logger::trace("WerteArray hat die Groesse 2, alles okay");

      $userid = $wertarray[0];
      $token = $wertarray[1];

      Logger::trace("Werte aus Array: USERID=" . $userid . ' TOKEN=' . $token);

      return Logger::traceExitData(getUserNameAndPassFromToken($userid, $token));
    }
  } else {
    Logger::trace("Kein Cookie gefunden... Login erforderlich!");
  }

  return Logger::traceExitData([]);
}

function getFooterParameter(string $userid): array
{
  return [SHOW_FOOTER => getParameterBoolean(SHOW_FOOTER, $userid),
    SHOW_DONO_LINK => getParameterBoolean(SHOW_DONO_LINK, $userid),
    SHOW_ABOUT_LINK => getParameterBoolean(SHOW_ABOUT_LINK, $userid),
    SHOW_DATENSCHUTZ_LINK => getParameterBoolean(SHOW_DATENSCHUTZ_LINK, $userid),
    SHOW_IMPRESSUM_LINK => getParameterBoolean(SHOW_IMPRESSUM_LINK, $userid)];
}

function getFaviconFromUrl(string $url, $userid): void
{
  $base = 'https://t1.gstatic.com/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=%s&size=32';
  $defaultIcoUrl = 'https://t1.gstatic.com/favicon.ico';

  if (str_starts_with($url, 'http') && "1" === getParameter("LOAD_FAVICONS_FROM_GOOGLESERVICE", $userid)) {
    $url = parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST);
    $baseUrl = trim($url, SEPERATOR);

    $completeUrl = formatString($base, [$baseUrl]);

    if (urlExists($completeUrl) && urlExists($defaultIcoUrl)) {
      echo '<img src="' . $completeUrl . '" height="19" width="19" style="margin-bottom: 3px;margin-right:5px;"/>';
    } else {
      echo '      <i class="fa-regular fa-bookmark" style="padding-right: 7px;padding-left: 3px;"></i>';
    }
  } else {
    echo '      <i class="fa-regular fa-bookmark" style="padding-right: 7px;padding-left: 3px;"></i>';
  }
}

function urlExists(string $url): bool
{
  return !strpos($url, "404");
}

/**
 * Replaces all %s in a String by the given values
 *
 * @param string $word the string with %s
 * @param array $vars the array of the replacements
 * @return string the replaced string
 */
function formatString(string $word, array $vars = array()): string
{
  return vsprintf($word, $vars) ?? $word;
}

function isSubSubCategory(string $cat, string $userid): bool
{
  Logger::traceEntryData("CategoryId=" . $cat, "UserId=" . $userid);

  if ($cat == "0") {
    Logger::trace("Kategorie 0 (Default) wird uebersprungen...");
    return false;
  }

  $category = getCategory($cat, $userid);

  if (empty($category) || empty($category[0])) {
    return Logger::traceExitData(false);
  }

  if (empty($category[0][FIELD_PARENT]) || $category[0][FIELD_PARENT] == "0") {
    return Logger::traceExitData(false);
  }

  $parentCategory = getCategory($category[0][FIELD_PARENT], $userid);

  if (empty($parentCategory) || empty($parentCategory[0])) {
    return Logger::traceExitData(false);
  }

  if (empty($parentCategory[0][FIELD_PARENT]) || $parentCategory[0][FIELD_PARENT] == 0) {
    return Logger::traceExitData(false);
  }

  return Logger::traceExitData(true);
}

function getCategory(string $catId, string $userId): array
{
  Logger::traceEntryData("CatId=" . $catId, "UserId=" . $userId);

  $ergebnis = null;
  $db = new PDO(PROJECT_DATABASE) or die (FAILED_OPEN_DB);
  $sql = "SELECT ID, NAME, USERID, COLOR, PARENT FROM CATEGORY WHERE ID = :catid AND USERID = :userid";
  $stmt = $db->prepare($sql);
  $stmt->bindParam(PARAM_CATID, $catId);
  $stmt->bindParam(PARAM_USERID, $userId);

  Logger::trace($sql);

  $stmt->execute();

  // alle eingelesenen Datensätze ausgeben
  while ($row = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
    $ergebnis = $row;

    Logger::trace('Datensatz gefunden: ' . json_encode($row));
  }

  $db = null;

  return Logger::traceExitData($ergebnis);
}

function showInfo(string $msg): void
{
  echo '<div id="alert" class="alert alert-info" role="alert">' . $msg . '</div>';
}

function showError(string $msg):void
{
  echo '<div id="alert" class="alert alert-danger" role="alert">' . $msg . '</div>';
}
