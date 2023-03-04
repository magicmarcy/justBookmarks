<?php
require_once('functions.php');
require('Logger.php');
include('static/Konst.php');

$checkPassword = null;

$userDataFromCookie = checkCookie();
$userDataOkay = isset($userDataFromCookie['NAME']) && isset($userDataFromCookie['PASS']);

if (isset($_POST["submit"]) || $userDataOkay) {
  Logger::trace('index(): POST submit ready ...');

  $username = $_POST["username"];
  $password = hash('sha1', $_POST["password"]);

  if ($userDataOkay) {
    $username = $userDataFromCookie['NAME'];
    $password = $userDataFromCookie['PASS'];
  }

  Logger::trace('index(): Username: ' . $username . ' | Password: ' . $password);

  $checkPassword = checkLogin($username, $password);

  if ($checkPassword == OKAY) {

    processingToken();

    updateLastLoginWithUsernameAndPass($username, $password);

    if (!isset($_SESSION)) {
      session_set_cookie_params(time() + SESSION_COOKIE_LIFETIME);
      ini_set('session.gc_maxlifetime', time() + SESSION_COOKIE_LIFETIME);
      session_start();
    }

    $_SESSION['login'] = OKAY;

    $checkAddBookmarkCookie = checkAddCookie();

    if (!empty($checkAddBookmarkCookie)) {
      header("Location: addBookmark.php?title=" . $checkAddBookmarkCookie[0] . "&url=" . $checkAddBookmarkCookie[1]);
    } else {
      header("Location: main.php");
    }
  } else {
    Logger::trace("index(): +++++ Fehlgeschlagener Loginversuch! +++++");
  }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title><?php echo PROJECTSHORTDESC; ?></title>
  <meta name="description" content="<?php echo PROJECTSHORTDESC; ?>">
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="externals/bootstrap/5.2.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="externals/fontawesome/6.2.1/fontawesome.min.css">
  <link rel="stylesheet" href="externals/fontawesome/6.2.1/all.min.css">
  <script src="externals/jquery/3.6.3/jquery-3.6.3.min.js"></script>
  <script>
    setTimeout(function() {
        $('#alert').fadeOut('fast');
    }, <?php echo ALERT_TIMEOUT;?>);
  </script>
</head>
<body>

<div class="container-fluid ps-md-0">
  <div class="row g-0">
    <?php include ('includes/frontpage-left.php');?>
    <div class="col-md-8 col-lg-6">
      <div class="login d-flex align-items-center py-5">
        <div class="container">
          <div class="row">
            <div class="col-md-9 col-lg-8 mx-auto">
              <h3 class="login-heading project-font-style mb-4">Login</h3>

              <form action="<?php echo FORM_ACTION; ?>" method="post">
                <div class="form-floating mb-3">
                  <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username">
                  <label for="floatingInput">Username</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control" id="floatingPassword" name="password"
                         placeholder="Password">
                  <label for="floatingPassword">Password</label>
                </div>
                <div class="d-grid">
                  <button class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" name="submit"
                          type="submit">Einloggen
                  </button>
                </div>
                <?php if ("1" == getParameter(REGISTRATION_ALLOWED, 0)) {
                  Logger::trace("index(): Registrtierung offen, zeige Link an");
                  echo '<div class="register-link"><a href="register.php">Registrieren</a></div>';
                }
                ?>
              </form>

              <?php
              if ($checkPassword == NOKAY) {
                echo '<div id="alert" class="alert alert-danger alert-dismissible fade show" style="position:fixed; top: 0;right:0;width:100vW;text-align:center;" role="alert">Login falsch!</div>';
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
