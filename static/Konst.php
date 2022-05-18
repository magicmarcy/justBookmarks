<?php

const PROJECTNAME             = "justBookmarks";
const PROJECT_SLOGAN          = "Deine Bookmarks, überall verfügbar, in jedem Browser.";
const PROJECTVERSION          = "0.5.325";
const PROJECTSHORTDESC        = PROJECTNAME . " | Deine Bookmarks, überall verfügbar, in jedem Browser!";
const PROJECTLONGDESC         = PROJECTNAME . " ist ein einzigartiger Bookmarking-Dienst.";

const SESSION_COOKIE_LIFETIME = 2592000; // 30 Tage (60 * 60 * 24 * 30)
const ALERT_TIMEOUT           = 5000;

const REGISTER_WARNING        = "Hinweis: Bitte verwende hier kein Passwort, dass du auch woanders verwendest. Die Seite verfügt derzeit 
                                 über keine HTTPS-Verschlüsselung daher wird deine Passworteingabe erst auf dem Server verschlüsselt (und
                                 in der Datenbank) - nicht aber auf dem Weg dort hin!";

const PROJECT_STARTPAGE       = 'index.php';

const PROJECT_DATABASE        = "sqlite:db/bookmarkservice.db";

const TITLE_LOGIN             = 'Login';
const FORM_ACTION             = 'index.php';
const DASHBOARD               = 'main.php';
CONST CAT_URL_PREFIX          = 'cat';

const OKAY                    = '1';
const NOKAY                   = '0';

const VERSION_TITLE_TEXT      = "Version " . PROJECTVERSION . "&#013;&#013;"
                              . "FIXED:&#013;"
                              . "- Parameter Verwendung&#013;&#013;"
                              . "UPDATES:&#013;"
                              . "- Löschen von Bookmarks&#013;"
                              . "- Löschen von Kategorien&#013;"
                              . "- Login wird sich für 30 Tage gemerkt (wird beim manuellen Logout verworfen)&#013;"
                              . "- Fehlerhandling verbessert&#013;&#013;"
                              . "STILL NOT WORKING:&#013;"
                              . "- Eigene Sortierung von Bookmarks&#013;";


/* PARAMETER */
const CATEGORY_BASE_COLOR     = "CATEGORY_BASE_COLOR";
const SHOW_UPLOAD_TAB         = "SHOW_UPLOAD_TAB";
const SHOW_SETTINGS_TAB       = "SHOW_SETTINGS_TAB";
const SHOW_PROFILE_TAB        = "SHOW_PROFILE_TAB";
const SHOW_TAG_TAB            = "SHOW_TAG_TAB";
const BASETARGET              = "BASETARGET";
const REGISTRATION_ALLOWED    = "REGISTRATION_ALLOWED";

const NO_USER                 = "0";

const PARAM_ID                = ":id";
const PARAM_USERID            = ":userid";
const PARAM_CATID             = ":catid";
const PARAM_CATNAME           = ":catName";
const PARAM_USERNAME          = ":username";
const PARAM_PASS              = ":pass";
const PARAM_EMAIL             = ":email";
const PARAM_CREATED           = ":created";
const PARAM_LASTLOGIN         = ":lastlogin";
const PARAM_NAME              = ":name";
const PARAM_URL               = ":url";
const PARAM_TAGS              = ":tags";
const PARAM_BOOKMARKID        = ":bookmarkid";
const PARAM_TOKEN             = ":token";


const PARAM_REGISTER_WARNING  = "SHOW_REGISTER_WARNING";

const FIELD_ID                  = "ID";
const FIELD_NAME                = "NAME";
const FIELD_USERID              = "USERID";
const FIELD_COLOR               = "COLOR";
const FIELD_CATEGORYID          = "CATEGORYID";
const FIELD_URL                 = "URL";
const FIELD_TAGS                = "TAGS";
const FIELD_VALUE               = "VALUE";

const USERNAME_MIN_CHARS      = 5;
const PASSWORD_MIN_CHARS      = 8;



