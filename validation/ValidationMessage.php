<?php

/**
 * Klasse fuer die unterschiedlichen Nachrichten
 * Eigentlich ein ENUM aber in PHP wohl besser das so zu machen ;-)
 */
abstract class ValidationMessage {

  const EMPTY                              = "";
  const VALIDIERUNG_ERFOLGREICH            = 'Die Vaidierung war erfolgreich';
  const USERNAMECONTAINSFORBIDDENCHARS     = "Der Username enthält ungültige Zeichen";
  const USERNAMEALREADYEXISTS              = "Der ausgewählte Username kann nicht verwendet werden";
  const USERNAMENOTENOUGHCHARS             = "Der Username muss mindestens " . USERNAME_MIN_CHARS . " Zeichen lang sein";
  const USERNAME_EMPTY                     = "Das Feld 'Username' darf nicht leer sein";
  const EMAIL_EMPTY                        = "Das Feld 'Email-Adresse' darf nicht leer sein";
  const PASSWORD_EMPTY                     = "Das Feld 'Password' darf nicht leer sein";
  const PASSWORDBEST_EMPTY                 = "Das Feld 'Passwort bestätigen' darf nicht leer sein";
  const EMAILNOTOKAY                       = "Die Email-Adresse ist fehlerhaft";
  const EMAILDOMAINNOTOKAY                 = "Registrierungen sind momentan nur von .de- und .com-Adressen zugelassen";
  const EMAILALREADYEXISTS                 = "Diese Email-Adresse kann nicht zur Registrierung verwendet werden";
  const PASSWORDSDOESNTMATCH               = "Das Passwort stimmt nicht mit der Passwortbestätigung überein";
  const PASSWORDWITHEMPTYCHARS             = "Das Passwort darf keine Leerzeichen enthalten";
  const PASSWORDTOOSHORT                   = "Das Passwort muss mindestens " . PASSWORD_MIN_CHARS . " Zeichen lang sein";
}
