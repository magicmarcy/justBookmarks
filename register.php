<?php
require_once('functions.php');
require_once('validation.php');
require('Logger.php');
include('static/Konst.php');

if (!"1" ==getParameter(REGISTRATION_ALLOWED, 0)) {
  Logger::trace("index(): Registrtierung geschlossen, leite zur Loginseite weiter");
  header("Location: index.php");
}

Logger::trace("register(): Registrierungsseite aufgerufen");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <title><?php echo PROJECTSHORTDESC; ?></title>
  <meta name="description" content="<?php echo PROJECTSHORTDESC; ?>">
  <link rel="icon" href="images/logo.png" type="image/png">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet"
        href="externals/bootstrap/5.2.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="externals/fontawesome/6.0.0/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdn.magicmarcy.de/fontawesome/fontawesome-free-6.2.1-web/css/all.min.css">
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
              <h3 class="login-heading project-font-style mb-4">Registrieren</h3>

              <form action="<?php echo 'register.php'; ?>" method="post">
                <div class="form-floating mb-3">
                  <input type="text" name="username" class="form-control" id="username" placeholder="Username" value="<?php echo !empty($username) ? $username : '';?>">
                  <label for="floatingInput">Login-Username</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="text" name="email" class="form-control" id="email" placeholder="Email" value="<?php echo !empty($email) ? $email : '';?>">
                  <label for="floatingInput">Email</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password"">
                  <label for="floatingPassword">Password</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control" id="passwordbest" name="passwordbest" placeholder="Password">
                  <label for="floatingPassword">Password bestätigen</label>
                </div>
                <div class="d-grid">
                  <button class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" name="submit"
                          type="submit">Registrieren
                  </button>
                </div>

                <div class="register-link"><a href="index.php">Einloggen</a></div>
              </form>

              <?php
              if ("1" == getParameter(PARAM_REGISTER_WARNING, 0)) {
                echo '<div class="register-warning">';
                echo REGISTER_WARNING;
                echo '<div>';
              }
              ?>

              <?php
              if (isset($_POST["submit"])) {
                Logger::trace('register(): POST submit ready ...');

                $username = $_POST["username"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $passwordbest = $_POST["passwordbest"];

                Logger::trace('register(): Username: ' . $username . ' | Email: ' . $email . ' Passwort: ' . $password . ' Passwort bestätigt: ' . $passwordbest);

                $checkRegistration = checkRegistrationInfos($username, $email, $password, $passwordbest);
                if ($checkRegistration) {
                  echo '<div id="alert" class="alert alert-info alert-dismissible fade show" style="position:fixed; top: 0;right:0;width:100vW;text-align:center;" role="alert">';
                  echo '<h4 class="alert-heading">Registrierung erfolgreich!</h4>';
                  echo '<p>Sie können Sie nun mit ihrem Usernamen und Passwort <a href="index.php" class="alert-link" target="_top">hier</a> einloggen</p>';
                  echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                  echo '<span aria-hidden="true">&times;</span>';
                  echo '</button>';
                  echo '</div>';
                } else {
                  Logger::trace("register(): Registration fehlgeschlagen!");
                }
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
