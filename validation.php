<?php

require ('validation/ValidationMessage.php');
require ('validation/ValidationResult.php');
require ('validation/ValidationType.php');

/*
 * VALIDATE REGISTRATION - Hier werden alle Validierungen durchgefuehrt
 */
function validateRegistration(string $username, string $email, string $password, string $passwordbest): ValidationResult  {
  $validationResult = checkEmptyFields($username, $email, $password, $passwordbest);

  if ($validationResult->getValidationType() === ValidationType::ERROR) {
    return $validationResult;
  }

  $validationResult = validateUsername($username);

  if ($validationResult->getValidationType() === ValidationType::ERROR) {
    return $validationResult;
  }

  $validationResult = validateEmail($email);

  if ($validationResult->getValidationType() === ValidationType::ERROR) {
    return $validationResult;
  }

  $validationResult = validatePassword($password, $passwordbest);

  if ($validationResult->getValidationType() === ValidationType::ERROR) {
    return $validationResult;
  }

  return new ValidationResult(ValidationType::SUCCESS, ValidationMessage::EMPTY);
}

/**
 * Prueft ob alle uebergebenen Felder gefuellt sind (nicht ob diese korrekt gefuellt sind)
 *
 * @param string $username      Der Username
 * @param string $email         Die Email-Adresse
 * @param string $password      Das Passwort
 * @param string $passwordbest  Das Passwort bestaetigt
 * @return ValidationResult
 */
function checkEmptyFields(string $username, string $email, string $password, string $passwordbest): ValidationResult {
  Logger::trace("checkEmptyFields(): Entry -> USERNAME=" . $username . ' EMAIL=' . $email . ' PASSWORD=' . $password . ' PASSWORDBEST=' . $passwordbest);

  if (empty($username)) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::USERNAME_EMPTY);
  }

  if (empty($email)) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::EMAIL_EMPTY);
  }

  if (empty($password)) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::PASSWORD_EMPTY);
  }

  if (empty($passwordbest)) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::PASSWORDBEST_EMPTY);
  }

  Logger::trace("checkEmptyFields(): Validierung erfolgreich");
  return new ValidationResult(ValidationType::SUCCESS, ValidationMessage::EMPTY);
}

/**
 * Verschiedenste Methoden um einen Usernamen zu verifizieren
 *
 * @param string $username Der zu pruefende Username
 * @return ValidationResult ValidationType::SUCCESS=okay, ValidationType::ERROR=nokay
 */
function validateUsername(string $username): ValidationResult {
  Logger::trace("validateUsername(): Entry -> USERNAME=" . $username);

  if (containsForbiddenChars($username)) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::USERNAMECONTAINSFORBIDDENCHARS);
  }

  if (strlen($username) < USERNAME_MIN_CHARS) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::USERNAMENOTENOUGHCHARS);
  }

  if (existsUsername($username)) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::USERNAMEALREADYEXISTS);
  }

  Logger::trace("validateUsername(): Validierung erfolgreich");
  return new ValidationResult(ValidationType::SUCCESS, ValidationMessage::EMPTY);
}

/**
 * Validierungsmethode fuer die Email-Adresse. Hier wird geprueft, ob die Email ein @-Zeichen enthaelt und die Endung .de oder .com
 * enthaelt. Des Weiteren wird geprueft, ob die Emailadresse bereits in der Datenbank vorhanden ist
 *
 * @param string $email Die zu pruefende Email
 * @return ValidationResult ValidationResult::SUCCESS=okay, ValidationResult::ERROR=nokay
 */
function validateEmail(string $email): ValidationResult {
  Logger::trace("validateEmail(): Entry -> EMAIL=" . $email);

  if (!str_contains($email, "@")) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::EMAILNOTOKAY);
  }

  if (!str_ends_with(strtolower(trim($email)), ".com") && !str_ends_with(strtolower(trim($email)), ".de")) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::EMAILDOMAINNOTOKAY);
  }

  if (existsEmail($email)) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::EMAILALREADYEXISTS);
  }

  Logger::trace("validateEmail(): Validierung erfolgreich");
  return new ValidationResult(ValidationType::SUCCESS, ValidationMessage::EMPTY);
}

/**
 * Validierungsmethode fuer die Passworteingaben
 *
 * @param string $password Das zu pruefende Passwort
 * @param string $passwordbest Das zu pruefende Passwort aus Passwort bestaetigen
 * @return ValidationResult ValidationResult::SUCCESS=okay, ValidationResult::ERROR=nokay
 */
function validatePassword(string $password, string $passwordbest): ValidationResult {
  Logger::trace("validatePassword(): Entry -> PASSWORD=" . $password . ' PASSWORDBEST=' . $passwordbest);

  if (strlen($password) < PASSWORD_MIN_CHARS) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::PASSWORDTOOSHORT);
  }

  if ($password !== $passwordbest) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::PASSWORDSDOESNTMATCH);
  }

  if (str_contains($password, ' ') || str_contains($passwordbest, ' ')) {
    return new ValidationResult(ValidationType::ERROR, ValidationMessage::PASSWORDWITHEMPTYCHARS);
  }

  Logger::trace("validatePassword(): Validierung erfolgreich");
  return new ValidationResult(ValidationType::SUCCESS, ValidationMessage::EMPTY);
}

/**
 * Prueft, ob der uebergebene Nutzername bereits in der Datenbank vorhanden ist. Dabei wird die Gross- und Kleinschreibung
 * des Usernamens ignoriert!
 *
 * @param string $username Der zu pruefende Username
 * @return bool true=Username existiert, false=Username nicht gefunden
 */
function existsUsername(string $username): bool {
  Logger::trace('existsUsername(): Enter -> USERNAME=' . $username);

  $lowerUsername = strtolower($username);

  $db = new PDO('sqlite:db/bookmarkservice.db');
  $sql = "SELECT ID, NAME, EMAIL, PASS, VERIFIED, CREATED, LASTLOGIN FROM USER WHERE LOWER(NAME) = :username";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_USERNAME, $lowerUsername);

  Logger::trace('existsUsername(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  if (!empty($res)) {
    Logger::trace('existsUsername(): Exit -> true');
    return true;
  }

  $db = null;

  Logger::trace('existsUsername(): Exit -> false');
  return false;
}

/**
 * Prueft, ob die uebergebene Emailadresse bereits in der Datenbank vorhanden ist. Dabei wird die Gross- und Kleinschreibung
 * dee Email ignoriert!
 *
 * @param string $email Die zu pruefende Email
 * @return bool true=Email existiert, false=Email nicht gefunden
 */
function existsEmail(string $email): bool {
  Logger::trace('existsEmail(): Enter -> EMAIL=' . $email);

  $lowerEmail = strtolower($email);

  $db = new PDO('sqlite:db/bookmarkservice.db');
  $sql = "SELECT ID, NAME, EMAIL, PASS, VERIFIED, CREATED, LASTLOGIN FROM USER WHERE LOWER(EMAIL) = :email";
  $stmt = $db -> prepare($sql);
  $stmt -> bindParam(PARAM_EMAIL, $lowerEmail);

  Logger::trace('existsEmail(): Folgender SQL wird ausgefuehrt: ' . $sql);

  $stmt -> execute();
  $res = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  if (!empty($res)) {
    Logger::trace('existsEmail(): Exit -> true');
    return true;
  }

  $db = null;

  Logger::trace('existsEmail(): Exit -> false');
  return false;
}


