<?php

/**
 * Liefert alle Kategorien der uebergebenen User-ID, sortiert nach NAME zurueck.
 */
function getCategorieListByUserId($userid): array {
  Logger::trace('getCategorieListByUserId(): Enter -> ' . $userid);

  $categories = [];

  if (!isset($userid)) {
    Logger::error('getCategorieListByUserId(): Keine User-ID erhalten!');
    return $categories;
  }

  $db = new PDO(PROJECT_DATABASE) or die ("failed to open db");
  $sql = "SELECT ID, NAME, USERID, COLOR FROM CATEGORY WHERE USERID = :id ORDER BY 2";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_ID, $userid);

  Logger::trace('getCategorieListByUserId(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach($res as $row) {
    $categories[] = ['ID' => $row['ID'], 'NAME' => $row['NAME'], 'USERID' => $row['USERID'], 'COLOR' => $row['COLOR']];

    Logger::trace('getCategorieListByUserId(): Datensatz erzeugt: ID=' . $row[FIELD_ID] . ' NAME=' . $row[FIELD_NAME] . ' USERID=' . $row[FIELD_USERID] . ' COLOR=' . $row[FIELD_COLOR]);
  }

  if (empty($categories)) {
    Logger::warn('getCategorieListByUserId(): Es konnten keine Datensaetze ermittelt werden.');
  } else {
    Logger::trace('getCategorieListByUserId(): Zuweisung durchlaufen, ' . sizeof($categories) . ' Ergebnisse erzeugt, gebe Ergebnis zurueck');
  }

  $db = null;

  Logger::trace('getCategorieListByUserId(): Exit -> ()');
  return $categories;
}

function getNumberOfBookmarksById($id, $userid) :int {
  Logger::trace('getNumberOfBookmarksById(): Enter -> ID=' . $id . ' USERID=' . $userid);

  $bookmarks = [];

  if (!is_numeric($id) || !isset($userid)) {
    Logger::error('getNumberOfBookmarksById(): Keine ID erhalten oder ID keine Zahl (ID=' . $id . ')');
    return sizeof($bookmarks);
  }

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT ID, CATEGORYID, USERID, NAME, URL, TAGS FROM BOOKMARK WHERE CATEGORYID = :id AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_ID, $id);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::trace('getNumberOfBookmarksById(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  if (empty($res)) {
    Logger::warn('getNumberOfBookmarksById(): Es konnten keine Datensaetze ermittelt werden.');
  } else {
    Logger::trace('getNumberOfBookmarksById(): ' . sizeof($res) . ' Bookmarks gelesen');
  }

  $db = null;

  Logger::trace('getNumberOfBookmarksById(): Exit -> ' . sizeof($res));
  return sizeof($res);
}

function checkLogin($username, $password): string {
  Logger::trace('checkLogin(): Entry');

  Logger::trace('checkLogin(): username: ' . $username . ' password: ' . $password);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT ID, NAME, EMAIL, PASS, VERIFIED, CREATED, LASTLOGIN FROM USER WHERE NAME = '" . $username . "' AND LOWER(PASS) = '" . strtolower($password) . "'";
  $results = $db->query($sql);
  Logger::trace('checkLogin(): Folgender SQL wird ausgefuehrt: ' . $sql);
  $ergebnis[] = array();

  // alle eingelesenen Datensätze ausgeben
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $ergebnis[] = $row;
  }

  // Da hier auch ein leerer Satz ein Ergebnis ist, muss das Array exakt 2 groß sein, damit der User mit dem Pass gefunden wurde
  if (count($ergebnis) === 2) {
    Logger::trace("checkLogin(): User gefunden, Ergebnis in SessionCookie speichern");

    session_start();
//    session_set_cookie_params(time() + 86400);

    $_SESSION['userdata'] = $ergebnis[1];

    Logger::trace('checkLogin(): Exit -> ' . OKAY);
    return OKAY;
  } else {
    Logger::warn("checkLogin(): User konnte nicht gefunden werden oder Zugang falsch!");

    Logger::trace('checkLogin(): Exit -> ' . NOKAY);
    return NOKAY;
  }
}

function getUserNameAndPassFromToken($userid, $token): array {
  Logger::trace('getUserNameAndPassFromToken(): Entry');

  Logger::trace('getUserNameAndPassFromToken(): USERID: ' . $userid . ' TOKEN: ' . $token);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT NAME, PASS FROM USER WHERE ID = '" . $userid . "' AND TOKEN = '" . hash('sha1', $token) . "'";
  $results = $db->query($sql);
  Logger::trace('getUserNameAndPassFromToken(): Folgender SQL wird ausgefuehrt: ' . $sql);
  $ergebnis[] = array();

  // alle eingelesenen Datensätze ausgeben
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $ergebnis[] = $row;
  }

  // Da hier auch ein leerer Satz ein Ergebnis ist, muss das Array exakt 2 groß sein, damit der User mit dem Pass gefunden wurde
  if (count($ergebnis) === 2) {
    Logger::trace("getUserNameAndPassFromToken(): User gefunden, Ergebnis in SessionCookie speichern");

    return $ergebnis[1];
  } else {
    Logger::warn("getUserNameAndPassFromToken(): User konnte nicht gefunden werden oder Zugang falsch!");

    return [];
  }
}

function getCategoryNameById($catid, $userid) {
  Logger::trace('getCategoryNameById(): Enter -> CATID=' . $catid . ' USERID=' . $userid);

  $categorieName = '';

  if (!isset($catid) || !isset($userid)) {
    Logger::error('getCategoryNameById(): Keine Kategorie-ID erhalten!');
    return $categorieName;
  }

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT NAME FROM CATEGORY WHERE ID = :catid AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(':catid', $catid);
  $stmt -> bindParam(':userid', $userid);

  Logger::trace('getCategoryNameById(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach($res as $row) {
    $categorieName = $row['NAME'];

    Logger::trace('getCategoryNameById(): Datensatz erzeugt: NAME=' . $categorieName);
  }

  if (!isset($categorieName)) {
    Logger::warn('getCategoryNameById(): Name der Kategorie konnte nicht gefunden werden.');
  } else {
    Logger::trace('getCategoryNameById(): Ergebnis erzeugt, gebe Ergebnis zurueck');
  }

  $db = null;

  Logger::trace('getCategoryNameById(): Exit -> ' . $categorieName);
  return $categorieName;
}

function getBookmarksByCategoryId($categoryid, $userid): array {
  Logger::trace('getBookmarksByCategoryId(): Enter -> CATID=' . $categoryid . ' USERID=' . $userid);

  $bookmarks = [];

  if (!isset($categoryid) || !isset($userid)) {
    Logger::error('getBookmarksByCategoryId(): Keine Kategorie-ID erhalten!');
    return $bookmarks;
  }

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT ID, CATEGORYID, USERID, NAME, URL, TAGS FROM BOOKMARK WHERE CATEGORYID = :catid AND USERID = :userid ORDER BY ID DESC";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(':catid', $categoryid);
  $stmt -> bindParam(':userid', $userid);

  Logger::trace('getBookmarksByCategoryId(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach($res as $row) {
    $bookmarks[] = ['ID' => $row['ID'], 'CATEGORYID' => $row['CATEGORYID'], 'USERID' => $row['USERID'], 'NAME' => $row['NAME'], 'URL' => $row['URL'], 'TAGS' => $row['TAGS']];

    Logger::trace('getCategorieListByUserId(): Datensatz erzeugt: ID=' . $row['ID'] . ' CATEGORYID=' . $row['CATEGORYID'] . ' USERID=' . $row['USERID'] . ' NAME=' . $row['NAME'] . ' URL=' . $row['URL'] . ' TAGS=' . $row['TAGS']);
  }

  if (empty($bookmarks)) {
    Logger::warn('getCategorieListByUserId(): Es konnten keine Datensaetze ermittelt werden.');
  } else {
    Logger::trace('getCategorieListByUserId(): Zuweisung durchlaufen, ' . sizeof($bookmarks) . ' Ergebnisse erzeugt, gebe Ergebnis zurueck');
  }

  $db = null;

  Logger::trace('getCategorieListByUserId(): Exit -> ()');
  return $bookmarks;
}

function console($msg) {
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
function getParameter($parametername, $userid): string {
  Logger::trace('getParameter(): Enter -> PARAMETERNAME=' . $parametername . ' USERID=' . $userid);

  $param = [];
  $paramValue = '';

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT ID, NAME, VALUE, USERID FROM PARAMETER WHERE NAME = :parametername";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(':parametername', $parametername);

  Logger::trace('getParameter(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  foreach($res as $row) {
    $param[] = ['ID' => $row['ID'], 'NAME' => $row['NAME'], 'VALUE' => $row['VALUE'], 'USERID' => $row['USERID']];

    Logger::trace('getParameter(): Datensatz erzeugt: ID=' . $row['ID'] . ' NAME=' . $row['NAME'] . ' VALUE=' . $row['VALUE'] . ' USERID=' . $row['USERID']);
  }

  if (empty($param)) {
    Logger::warn("getParameter(): Keine Daten gefunden.");
  }

  foreach ($param as $parameter) {
    if ($parameter['USERID'] == $userid) {
      $paramValue = $parameter['VALUE'];
      break;
    }
  }

  if (empty($paramValue)) {
    foreach ($param as $parameter) {
      if ($parameter['USERID'] == NO_USER) {
        $paramValue = $parameter['VALUE'];
        break;
      }
    }
  }

  Logger::trace("getParameter(): Exit -> " . $paramValue);
  return $paramValue;
}

function getParameterBoolean($parametername, $userid): bool {
  Logger::trace("getParameterBoolean(): Enter -> PARAMETERNAME=" . $parametername . " USERID=" . $userid);

  $result = false;
  $parameterValue = getParameter($parametername, $userid);

  Logger::trace("getParameterBoolean(): ParameterValueResult from DB=" . $parameterValue);

  if (isset($parameterValue) && !empty($parameterValue) && $parameterValue == '1') {
    $result = true;
  }

  Logger::trace("getParameterBoolean(): Exit -> " . $result);
  return $result;
}

function hasForbiddenCharsInCategoryname(string $category): bool {
  $paramForbiddenChars = getParameter(PARAM_FORBIDDEN_CHARS, NO_USER);

  Logger::trace("hasForbiddenCharsInCategoryname(): PARAMETER PARAM_FORBIDDEN_CHARS=" . $paramForbiddenChars);

  $chars = explode(FORBIDDEN_CHAR_DELIMITER, $paramForbiddenChars);

  if (isset($chars)) {
    foreach ($chars as $char) {
      Logger::trace("hasForbiddenCharsInCategoryname(): Pruefe auf Vorkommen von: " . $char);

      if (isset($char) && !empty($char) && str_contains($category, $char)) {
        Logger::trace("hasForbiddenCharsInCategoryname(): Verbotenes Zeichen <" . $char . "> in <" . $category . "> gefunden");
        return true;
      }
    }
  }

  return false;
}

function categoryExists($categoryName, $userid): bool {
  Logger::trace('categoryExists(): Enter -> CATEGORY=' . $categoryName . ' USERID=' . $userid);

  $categoryExists = false;

  if (!isset($categoryName) || !isset($userid)) {
    Logger::error('categoryExists(): Keine Kategorie oder UserId erhalten!');
    return $categoryExists;
  }

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT ID, NAME, USERID, COLOR FROM CATEGORY WHERE LOWER(NAME) = :catName AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $catInLowerCase = strtolower($categoryName);
  $stmt -> bindParam(PARAM_CATNAME, $catInLowerCase);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::trace('categoryExists(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  if (!empty($res)) {
    $categoryExists = true;
  }

  $db = null;

  Logger::trace('categoryExists(): Exit -> ' . $categoryExists);
  return $categoryExists;
}


function validateCategory($category, $userid): bool {
  Logger::trace("validateCategory(): Enter -> CATEGORY=" . $category . ' USERID=' . $userid);

  if (trim($category) == '') {
    Logger::trace("validateCategory(): Kategorie darf nicht leer sein!");
    echo '<div id="alert" class="alert alert-danger" role="alert">Du musst einen Kategorienamen angeben!</div>';
    return false;
  } else if (hasForbiddenCharsInCategoryname($category)) {
    Logger::trace("validateCategory(): Verbotene Zeichen enthalten");
    echo '<div id="alert" class="alert alert-danger" role="alert">Der Kategoriename darf keine Sonderzeichen enthalten!</div>';
    return false;
  } else if (strlen($category) > CAT_MAX_CHARS) {
    Logger::trace("validateCategory(): Kategoriename ist zu lang! +++ Achtung! +++ Vermutlich Request-Manipulation!!!!");
    echo '<div id="alert" class="alert alert-danger" role="alert">Der Kategoriename darf nur ' . CAT_MAX_CHARS . ' Zeichen enthalten!</div>';
    return false;
  } else if (strtolower($category) == 'default') {
    Logger::trace("validateCategory(): Kategorie darf nicht default heissen!");
    echo '<div id="alert" class="alert alert-danger" role="alert">Du kannst keine weitere Kategorie mit dem Namen "Default" anlegen!</div>';
    return false;
  } else if (categoryExists($category, $userid)) {
    Logger::trace("validateCategory(): Kategorie existiert bereits");
    echo '<div id="alert" class="alert alert-danger" role="alert">Kategorie existiert bereits!</div>';
    return false;
  } else {
    Logger::trace("validateCategory(): Validierung durchgefuehrt, Kategorie existiert noch nicht");
  }

  Logger::trace("validateCategory(): Exit -> true");
  return true;
}

function validateColor($color): bool {
  if (!str_starts_with($color, '#')) {
    return false;
  }

  if (strlen($color) !== 7) {
    return false;
  }

  return true;
}

function addNewCategory($categoryname, $color, $userid):bool {
  Logger::trace('addNewCategory(): Enter -> CATEGORYNAME=' . $categoryname . ' COLOR=' . $color . ' USERID=' . $userid);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "INSERT INTO CATEGORY (NAME, USERID, COLOR) VALUES (:categoryname, :userid, :color)";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(':categoryname', $categoryname);
  $stmt -> bindParam(':userid', $userid);
  $stmt -> bindParam(':color', $color);

  Logger::trace('getParameter(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();

  return true;
}

/**
 * Prueft, ob der uebergebene Ausdruck nur aus Zahlen und Buchstaben besteht. Ein Vorkommen jedes anderen Zeichens liefert ein false
 * zurueck. Das gilt auch fuer Leerzeichen!
 *
 * @param $value    String: Der zu pruefende String
 * @return bool     true=okay, false=falsch
 */
function containsForbiddenChars(string $value): bool {
  return !ctype_alnum($value);
}


/**
 * Prueft eine Registrierung und validiert dazu zunaechst alle Eingaben (inkl. Fehlermeldungshandling)
 */
function checkRegistrationInfos(string $username, string $email, string $passwort, string $passwortbest): bool {

  $validationResult = validateRegistration($username, $email, $passwort, $passwortbest);

  if ($validationResult->getValidationType() == ValidationType::ERROR) {
    Logger::warn('checkRegistrationInfos(): ERROR: ' . $validationResult->getValidationMessage());
    echo '<div id="alert" class="alert alert-danger" style="position:fixed; top: 0;right:0;width:100vW;text-align:center;" role="alert">' . $validationResult->getValidationMessage() . '</div>';
    return false;

  } else if ($validationResult->getValidationType() == ValidationType::SUCCESS) {
    Logger::warn('checkRegistrationInfos(): SUCCESS: Validierung erfolgreich durchlaufen, fuehre Registrierung durch');

    return addNewUser($username, $email, $passwort, $passwortbest);
  }

  return true;
}

function addNewUser(string $username, string $email, string $password, string $passwordbest): bool {
  Logger::trace('addNewUser(): Enter -> USERNAME=' . $username . ' EMAIL=' . $email . ' PASSWORD=' . $password . ' PASSWORDBEST=' . $passwordbest);

  $lowerEmail = strtolower($email);
  $passSHA = hash('sha1', $password);
  $today = date("Y-m-d");
  $time = date("H:i");

  $created = $today . ' ' . $time;

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "INSERT INTO USER (NAME, EMAIL, PASS, VERIFIED, CREATED) VALUES (:username, :email, :pass, '1', :created)";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_USERNAME, $username);
  $stmt -> bindParam(PARAM_EMAIL, $lowerEmail);
  $stmt -> bindParam(PARAM_PASS, $passSHA);
  $stmt -> bindParam(PARAM_CREATED, $created);

  Logger::trace('addNewUser(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();

  $id = $db->lastInsertId();

  if (!is_numeric($id) || $id <= 0) {
    Logger::error("addNewUser(): Fehler beim Anlegen des neuen Users! Bitte Log beachten!");
    return false;
  }

  Logger::trace("addNewUser(): User erfolgreich angelegt! USERID=" . $id);
  return true;
}

function updateLastLogin(string $userid): bool {
  Logger::trace('updateLastLogin(): Enter -> USERID=' . $userid);

  $today = date("Y-m-d");
  $time = date("H:i");

  $created = $today . ' ' . $time;

  Logger::trace("updateLastLogin(): Setze LastLogin auf " . $created . ' fuer UserId=' . $userid);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "UPDATE USER SET LASTLOGIN = :lastlogin WHERE ID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_LASTLOGIN, $created);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::trace('updateLastLogin(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $result = $stmt -> execute();

  if (!$result) {
    Logger::error("updateLastLogin(): Fehler beim updaten des aktuellen Logins!");
    return false;
  }

  Logger::trace("updateLastLogin(): User-Login erfolgreich aktualisiert!");
  return true;
}

function updateLastLoginWithUsernameAndPass(string $username, string $password): void {
  $userid = getUserIdFromUsernameAndPass($username, $password);

  $result = updateLastLogin($userid);
}

function getUserIdFromUsernameAndPass(string $username, string $password): int {
  Logger::trace('getUserIdFromUsernameAndPass(): Entry -> USERNAME=' . $username . ' PASS=' . $password);

  $pass = hash('sha1', $password);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "SELECT ID, NAME, EMAIL, PASS, VERIFIED, CREATED, LASTLOGIN FROM USER WHERE NAME = '" . $username . "' AND LOWER(PASS) = '" . $pass . "'";
  $results = $db->query($sql);
  Logger::trace('getUserIdFromUsernameAndPass(): Folgender SQL wird ausgefuehrt: ' . $sql);
  $ergebnis[] = array();

  // alle eingelesenen Datensätze ausgeben
  while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
    $ergebnis[] = ['ID' => $row['ID'], 'NAME' => $row['NAME'], 'EMAIL' => $row['EMAIL'], 'PASS' => $row['PASS'], 'VERIFIED' => $row['VERIFIED'], 'CREATED' => $row['CREATED'], 'LASTLOGIN' => $row['LASTLOGIN']];
  }

  if (count($ergebnis) === 2) {
    var_dump($ergebnis);

    $newUserId = $ergebnis[1]['ID'];
    Logger::trace("getUserIdFromUsernameAndPass(): EXIT -> " . $newUserId);
    return (int) $newUserId;
  }

  return 0;
}

function addNewBookmark(string $newBookmarkName, string $newBookmarkUrl, string $newBookmarkTags, string $newBookmarkCatId, string $newBookmarkUserId): bool {
  Logger::trace('addNewBookmark(): Enter -> NAME=' . $newBookmarkName . ' URL=' . $newBookmarkUrl . ' TAGS=' . $newBookmarkTags . ' CATID=' . $newBookmarkCatId . ' USERID=' . $newBookmarkUserId);

  $tagsWithoutWhitespaces = str_replace(" ","",$newBookmarkTags);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "INSERT INTO BOOKMARK (CATEGORYID, USERID, NAME, URL, TAGS) VALUES (:catid, :userid, :name, :url, :tags)";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $newBookmarkCatId);
  $stmt -> bindParam(PARAM_USERID, $newBookmarkUserId);
  $stmt -> bindParam(PARAM_NAME, $newBookmarkName);
  $stmt -> bindParam(PARAM_URL, $newBookmarkUrl);
  $stmt -> bindParam(PARAM_TAGS, $tagsWithoutWhitespaces);

  Logger::trace('addNewBookmark(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();

  $id = $db->lastInsertId();

  if (!is_numeric($id) || $id <= 0) {
    Logger::error("addNewBookmark(): Fehler beim Anlegen des neuen Users! Bitte Log beachten!");
    return false;
  }

  Logger::trace("addNewBookmark(): Bookmark erfolgreich angelegt! ID=" . $id);
  return true;
}

function deleteBookmark($bookmarkid, $userid, $catid): bool {
  Logger::trace('deleteBookmark(): Enter -> BOOKMARKID=' . $bookmarkid . ' USERID=' . $userid . ' CATID=' . $catid);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "DELETE FROM BOOKMARK WHERE ID = :bookmarkid AND CATEGORYID = :catid AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_BOOKMARKID, $bookmarkid);
  $stmt -> bindParam(PARAM_USERID, $userid);
  $stmt -> bindParam(PARAM_CATID, $catid);

  Logger::trace('deleteBookmark(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  if ($rowCount != 1) {
    Logger::error("deleteBookmark(): Fehler beim Löschen des Bookmarks! Bitte Log beachten!");
    return false;
  }

  Logger::trace("deleteBookmark(): " . $rowCount . " Bookmark erfolgreich gelöscht!");
  return true;
}

function deleteCategory($userid, $catid): bool {
  Logger::trace('deleteCategory(): Enter -> USERID=' . $userid . ' CATID=' . $catid);

  if (isset($catid) && ($catid == "0" || $catid == 0)) {
    Logger::trace('deleteCategory(): Kategorie 0 uebergeben! Darf nicht geloescht werden! Breche Verarbeitung ab!');
    return false;
  }

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "DELETE FROM CATEGORY WHERE ID = :catid AND USERID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $catid);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::trace('deleteCategory(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  if ($rowCount != 1) {
    Logger::error("deleteCategory(): Fehler beim Löschen des Bookmarks! Bitte Log beachten!");
    return false;
  }

  $movingResult = moveBookmnarksFromCategoryToDefault($userid, $catid);

  Logger::trace("deleteCategory(): " . $rowCount . " Kategorie erfolgreich gelöscht!");
  Logger::trace("deleteCategory(): " . $movingResult . " Bookmarks erfolgreich verschoben!");
  return true;
}

function moveBookmnarksFromCategoryToDefault($userid, $catid): int {
  Logger::trace('moveBookmnarksFromCategoryToDefault(): Enter -> USERID=' . $userid . ' CATID=' . $catid);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "UPDATE BOOKMARK SET CATEGORYID = 0 WHERE USERID = :userid AND CATEGORYID = :catid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_CATID, $catid);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::trace('moveBookmnarksFromCategoryToDefault(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  if ($rowCount < 0) {
    Logger::error("moveBookmnarksFromCategoryToDefault(): Fehler beim Verschieben der Bookmarks! Bitte Log beachten!");
    return 0;
  }

  Logger::trace("moveBookmnarksFromCategoryToDefault(): " . $rowCount . " Bookmark erfolgreich zur Default-Kategorie verschoben!");
  return $rowCount;
}

function processingToken(): void {
  deleteCookieIfExists();
  createAndSetCookie();
}

function createAndSetCookie(): void {
  // Da der User hier bereits eingeloggt ist, stehen seine Angaben bereits im SessionCookie
  $userdata = $_SESSION['userdata'];
  $userid = $userdata['ID'];
  $randomString = generateRandomString();
  $randomStringSha1 = hash('sha1', $randomString);

  $cookieValue = $userid . '|' . $randomString;

  setcookie("justBookmarks", $cookieValue, time()+3600*24*30); // 31 Tage gueltig

  // jetzt Updaten wir das Token - verschluesselt - in der User Tabelle
  updateUserToken($userid, $randomStringSha1);
}

/**
 * Aktualisieren des User-Tokens. Wir muessen hier nichts zurueck bekommen weil es uns egal ist
 * ob das funktioniert hat. Im Zweifel muss sich der User neu einloggen.
 */
function updateUserToken(string $userid, string $token): void {
  Logger::trace('updateUserToken(): Enter -> USERID=' . $userid . ' TOKEN=' . $token);

  $db = new PDO('sqlite:db/bookmarkservice.db') or die ("failed to open db");
  $sql = "UPDATE USER SET TOKEN = :token WHERE ID = :userid";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_TOKEN, $token);
  $stmt -> bindParam(PARAM_USERID, $userid);

  Logger::trace('updateUserToken(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();

  $rowCount = $stmt->rowCount();

  if ($rowCount < 0) {
    Logger::error("updateUserToken(): Fehler beim Verschieben der Bookmarks! Bitte Log beachten!");
  }

  Logger::trace("updateUserToken(): " . $rowCount . " Bookmark erfolgreich zur Default-Kategorie verschoben!");
}

function generateRandomString(): string {
    $length = 25;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Ist bereits ein Cookie von hier vorhanden? Dann loeschen wir das erstmal
 */
function deleteCookieIfExists(): void {
  if (isset($_COOKIE['justBookmarks'])) {
    setcookie("justBookmarks","",time()-3600);
  }
}

function checkCookie(): array {
  Logger::trace("checkCookie(): Enter ->");

  if (isset($_COOKIE['justBookmarks'])) {
    Logger::trace("checkCookie(): Cookie gefunden... werte Inhalt aus");

    $wertarray = explode("|",$_COOKIE["justBookmarks"]);

    if (sizeof($wertarray) == 2) {
      Logger::trace("checkCookie(): WerteArray hat die Groesse 2, alles okay");

      $userid = $wertarray[0];
      $token = $wertarray[1];

      Logger::trace("checkCookie(): Werte aus Array: USERID=" . $userid . ' TOKEN=' . $token);

      return getUserNameAndPassFromToken($userid, $token);
    }
  } else {
    Logger::trace("checkCookie(): Kein Cookie gefunden... Login erforderlich!");
  }

  return [];
}

function getFooterParameter(string $userid): array {
  return [SHOW_FOOTER => getParameterBoolean(SHOW_FOOTER, $userid),
    SHOW_DONO_LINK => getParameterBoolean(SHOW_DONO_LINK, $userid),
    SHOW_ABOUT_LINK => getParameterBoolean(SHOW_ABOUT_LINK, $userid),
    SHOW_DATENSCHUTZ_LINK => getParameterBoolean(SHOW_DATENSCHUTZ_LINK, $userid),
    SHOW_IMPRESSUM_LINK => getParameterBoolean(SHOW_IMPRESSUM_LINK, $userid)];
}
