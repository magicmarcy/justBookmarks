<?php
require_once('functions.php');
require('Logger.php');
include('static/Konst.php');

$checkPassword = null;

$userDataFromCookie = checkCookie();
$userDataOkay = isset($userDataFromCookie[FIELD_NAME]) && isset($userDataFromCookie[FIELD_PASS]);

if (isset($_POST[POST_SUBMIT]) || $userDataOkay) {
  Logger::trace('POST submit ready ...');

  if ($userDataOkay) {
    $username = $userDataFromCookie[FIELD_NAME];
    $password = $userDataFromCookie[FIELD_PASS];
  } else {
    $username = $_POST[POST_USERNAME];
    $password = hash('sha1', $_POST[POST_PASSWORD]);
  }

  Logger::trace('Username: ' . $username . ' | Password: ' . $password);

  $checkPassword = checkLogin($username, $password);

  if ($checkPassword == OKAY) {
    processingToken();

    updateLastLoginWithUsernameAndPass($username, $password);

    if (!isset($_SESSION)) {
      session_set_cookie_params(time() + SESSION_COOKIE_LIFETIME);
      ini_set('session.gc_maxlifetime', time() + SESSION_COOKIE_LIFETIME);
      session_start();
    }

    $_SESSION[SESSION_LOGIN] = OKAY;

    $checkAddBookmarkCookie = checkAddCookie();

    if (!empty($checkAddBookmarkCookie)) {
      header("Location: addBookmark.php?title=" . $checkAddBookmarkCookie[0] . "&url=" . $checkAddBookmarkCookie[1]);
    } else {
      header("Location: main.php");
    }
  } else {
    Logger::trace("+++++ Fehlgeschlagener Loginversuch! +++++");
  }
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
  <link rel="stylesheet" href="<?=STYLE_BOOTSTRAPMIN_INDEX;?>">
  <link rel="stylesheet" href="<?=STYLE_FONTAWESOMEMIN_INDEX;?>">
  <link rel="stylesheet" href="<?=STYLE_FONTAWESOMEALLMIN_INDEX;?>">
  <script src="<?=JS_JQUERY;?>"></script>
  <script>
      setTimeout(function() {
          let alert = document.getElementById('alert');
          if (alert) {
              alert.fadeOut('fast');
          }
      }, <?=ALERT_TIMEOUT;?>);
  </script>
</head>
<body>

<div class="container-fluid ps-md-0">
  <div class="row g-0">
    <?php include_once('includes/frontpage-left.php');?>
    <div class="col-md-8 col-lg-6">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto">
              <h3 class="login-heading project-font-style mb-4"><?=LOGIN;?></h3>

              <form action="<?=FORM_ACTION;?>" method="post">
                <div class="form-floating mb-3">
                  <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username">
                  <label for="floatingInput"><?=USERNAME;?></label>
                </div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control" id="floatingPassword" name="password"
                         placeholder="Password">
                  <label for="floatingPassword"><?=PASSWORD;?></label>
                </div>
                <div class="d-grid">
                  <button class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" name="submit"
                          type="submit"><?=REG_EINLOGGEN;?>
                  </button>
                </div>
                <?php if ("1" == getParameter(REGISTRATION_ALLOWED, 0)) {
                  Logger::trace("Registrtierung offen, zeige Link an");
                  echo '<div class="register-link"><a href="register.php">' . REGISTRIEREN . '</a></div>';
                }
                ?>
              </form>

              <?php
              if ($checkPassword == NOKAY) {
                echo '<div id="alert" class="alert alert-danger alert-dismissible fade show"
                           style="position:fixed; top: 0;right:0;width:100vW;text-align:center;"
                           role="alert">' . LOGIN_FALSCH . '</div>';
              }?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
