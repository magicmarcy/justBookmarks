<?php
const PROJECTNAME = "justBookmarks";
const PROJECT_SLOGAN = "Deine Bookmarks, überall verfügbar, in jedem Browser.";
const PROJECTVERSION = "2023.1.0 alpha";
const PROJECTSHORTDESC = PROJECTNAME . " | Deine Bookmarks, überall verfügbar, in jedem Browser!";
const PROJECTLONGDESC = PROJECTNAME . " ist ein einzigartiger Bookmarking-Dienst.";

const DONATELINK = "https://magicmarcy.de/donate";
const DONATETEXT = "Donate";
const GITHUB_RELEASEINFO = "https://github.com/magicmarcy/justBookmarks/releases";

const SEPERATOR = "/";
const CSS_BASEFOLDER = "css";
const EXTERNAL_BASEFOLDER = "externals";
const STRING_EMPTY = "";

/* CSS */
const STYLE_DEFAULT_CSS = CSS_BASEFOLDER . SEPERATOR . "style.css";

/* BOOTSTRAP */
const STYLE_BOOTSTRAPMIN = "https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css";
const STYLE_BOOTSTRAPMIN_INDEX = EXTERNAL_BASEFOLDER . SEPERATOR . "bootstrap/5.2.3/css/bootstrap.min.css";

/* FONT AWESOME */
const STYLE_FONTAWESOMEMIN = EXTERNAL_BASEFOLDER . SEPERATOR . "fontawesome/6.0.0/fontawesome.min.css";
const STYLE_FONTAWESOMEMIN_INDEX = EXTERNAL_BASEFOLDER . SEPERATOR . "fontawesome/6.2.1/fontawesome.min.css";
const STYLE_FONTAWESOMEALLMIN = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css";
const STYLE_FONTAWESOMEALLMIN_INDEX = EXTERNAL_BASEFOLDER . SEPERATOR . "fontawesome/6.2.1/all.min.css";

/* JQUERY */
const JS_JQUERY = EXTERNAL_BASEFOLDER . SEPERATOR . "jquery/3.6.3/jquery-3.6.3.min.js";
const JS_JQUERY_360 = "https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js";

/* JAVASCRIPT */
const JS_DEFAULT = EXTERNAL_BASEFOLDER . SEPERATOR . "default.js";

/* GOOGLE FONTS */
const GOOGLE_FONTS = <<<EOD
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Oleo+Script+Swash+Caps&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
EOD;


const SESSION_COOKIE_LIFETIME = 2592000; // 30 Tage (60 * 60 * 24 * 30)
const ALERT_TIMEOUT = 5000;

const REGISTER_WARNING = "Hinweis: Bitte verwende hier kein Passwort, dass du auch woanders verwendest. Die Seite verfügt derzeit
                          über keine HTTPS-Verschlüsselung daher wird deine Passworteingabe erst auf dem Server verschlüsselt (und
                          in der Datenbank) - nicht aber auf dem Weg dort hin!";

const PROJECT_STARTPAGE = 'index.php';

const PROJECT_DATABASE = "sqlite:db/bookmarkservice.db";
const PROJECT_LOGFOLDERNAME = "log";
const DEFAULT_LOGFOLDER_PERMISSION = 0777;

const TITLE_LOGIN = 'Login';
const FORM_ACTION = 'index.php';
const DASHBOARD = 'main.php';
const CAT_URL_PREFIX = '?cat=';
const CAT_MAX_CHARS = 20;
const HASH = "#";

const OKAY = '1';
const NOKAY = '0';

const STR_EMPTY = "";

const DEFAULT_CAT_NAME = "Default";

const HEADLINE_CATEGORIES = "Kategorien";

/* PARAMETER */
const CATEGORY_BASE_COLOR = "CATEGORY_BASE_COLOR";
const SHOW_UPLOAD_TAB = "SHOW_UPLOAD_TAB";
const SHOW_BOOKMARKLET_TAB = "SHOW_BOOKMARKLET_TAB";
const SHOW_SETTINGS_TAB = "SHOW_SETTINGS_TAB";
const SHOW_PROFILE_TAB = "SHOW_PROFILE_TAB";
const SHOW_TAG_TAB = "SHOW_TAG_TAB";
const BASETARGET = "BASETARGET";
const REGISTRATION_ALLOWED = "REGISTRATION_ALLOWED";
const SHOW_FOOTER = "SHOW_FOOTER";
const SHOW_DONO_LINK = "SHOW_DONO_LINK";
const SHOW_ABOUT_LINK = "SHOW_ABOUT_LINK";
const SHOW_DATENSCHUTZ_LINK = "SHOW_DATENSCHUTZ_LINK";
const SHOW_IMPRESSUM_LINK = "SHOW_IMPRESSUM_LINK";

const NO_USER = "0";

/* QUERY PARAMS */
const PARAM_ID = ":id";
const PARAM_PARENT = ":parent";
const PARAM_USERID = ":userid";
const PARAM_CATID = ":catid";
const PARAM_CATNAME = ":catName";
const PARAM_COLOR = ":color";
const PARAM_USERNAME = ":username";
const PARAM_PASS = ":pass";
const PARAM_EMAIL = ":email";
const PARAM_CREATED = ":created";
const PARAM_LASTLOGIN = ":lastlogin";
const PARAM_NAME = ":name";
const PARAM_URL = ":url";
const PARAM_TAGS = ":tags";
const PARAM_POSITION = ":position";
const PARAM_BOOKMARKID = ":bookmarkid";
const PARAM_TOKEN = ":token";
const PARAM_FORBIDDEN_CHARS = "PARAM_FORBIDDEN_CHARS";
const PARAM_QUERY = ":query";

const PARAM_REGISTER_WARNING = "SHOW_REGISTER_WARNING";

const FIELD_ID = "ID";
const FIELD_NAME = "NAME";
const FIELD_EMAIL = "EMAIL";
const FIELD_PASS = "PASS";
const FIELD_VERIFIED = "VERIFIED";
const FIELD_CREATED = "CREATED";
const FIELD_LASTLOGIN = "LASTLOGIN";
const FIELD_USERID = "USERID";
const FIELD_COLOR = "COLOR";
const FIELD_PARENT = "PARENT";
const FIELD_POSITION = "POSITION";
const FIELD_CATEGORYID = "CATEGORYID";
const FIELD_URL = "URL";
const FIELD_TAGS = "TAGS";
const FIELD_VALUE = "VALUE";

const COOKIE_JUSTBOOKMARKS = "justBookmarks";
const COOKIE_ADDBOOKMARKTITLE = "addBookmarkTitle";
const COOKIE_ADDBOOKMARKURL = "addBookmarkUrl";
const COOKIE_DEFAULT_TIME = 3600;
const COOKIE_SEPERATOR = "|";

const USERNAME_MIN_CHARS = 5;
const PASSWORD_MIN_CHARS = 8;

const FAILED_OPEN_DB = "failed to open db";

const DATE_PATTERN_YMD = "Y-m-d";
const DATETIME_PATTERN_YMDHMSS = "Y-m-d H:i:s.v";
const TIME_PATTERN_HM = "H:i";
const LOG_FILEEXTENSION = ".log";

// Log-Messages
const NO_RECORDS_FOUND = "No records found.";
const RECORDS_FOUND = "%s records found";
const EXECUTE_SQL = "Execute SQL: %s";

/* MELDUNGSTEXTE */
const DELETE_BOOKMARK_SUCCESS = "Bookmark gelöscht";
const DELETE_BOOKMARK_ERROR = "Es ist ein Fehler beim Löschen des Bookmarks aufgetreten!";

const ADD_BOOKMARK_SUCCESS = "Bookmark hinzugefügt!";

const ADD_CATEGORY_SUCCESS = "Kategorie hinzugefügt!";
const ADD_CATEGORY_ERROR = "Es ist ein Fehler beim Anlegen der Kategorie aufgetreten!";

const DELETE_DEFAULT_ERROR = "Die Default-Kategorie kann nicht gelöscht werden!";

const DELETE_CATEGORY_SUCCEDD = "Kategorie gelöscht, Bookmarks verschoben!";
const DELETE_CATEGORY_ERROR = "Es ist ein Fehler beim Löschen der Kategorie aufgetreten!";

const NO_BOOKMARKS_IN_CATEGORY_HEADLINE = "Keine Bookmarks in dieser Kategorie vorhanden.";

const REGISTRIEREN = "Registrieren";
const REG_USERNAME = "Login-Username";
const REG_EMAIL = "Email";
const REG_PASSWORD = "Passwort";
const REG_PASSWORD_CONF = "Password best&auml;tigen";
const REG_EINLOGGEN = "Einloggen";
const USERNAME = "Username";
const NAME = "Name";
const URL = "URL";
const TAGS = "Tags";
const PASSWORD = "Passwort";
const LOGIN_FALSCH = "Login falsch";
const LOGIN = "Login";
const ABBRECHEN = "Abbrechen";
const HINZUFUEGEN = "Hinzuf&uuml;gen";
const BOOKMARK_HINZUFUEGEN = "Bookmark hinzuf&uuml;gen";

/* POST-, GET- und SESSION-DATA */
const POST_SUBMIT = "submit";
const POST_USERNAME = "username";
const POST_PASSWORD = "password";
const POST_PASSWORD_BEST = "passwordbest";
const POST_CATEGORYID = "categoryid";
const POST_NAME = "name";
const POST_EMAIL = "email";
const POST_URL = "url";
const POST_TAGS = "tags";
const POST_CATID = "catid";
const POST_USERID = "userid";
const POST_QUERY = "query";
const POST_DELETE_BOOKMARK_BOOKMARK_ID = "delete_bookmark_bookmarkid";
const POST_DELETE_BOOKMARK_CAT_ID = "delete_bookmark_catid";
const POST_DELETE_BOOKMARK_USER_ID = "delete_bookmark_userid";
const POST_DELETE_CAT_CAT_ID = "delete-category-catid";
const POST_DELETE_CAT_USER_ID = "delete-category-userid";

const SESSION_LOGIN = "login";
const SESSION_USERDATA = "userdata";

const GET_CATEGORY = "category";
const GET_CAT = "cat";
const GET_COLOR = "color";
const GET_PARENT_CATEGORY_ID = "parentcategoryid";
const GET_TITLE = "title";
const GET_URL = "url";
