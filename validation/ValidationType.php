<?php

/**
 * Klasse fuer die unterschiedlichen Validations-Ergebnis-Typen
 * Eigentlich ein ENUM aber in PHP wohl besser das so zu machen ;-)
 */
abstract class ValidationType {

  const ERROR = "ERROR";
  const SUCCESS = "SUCCESS";

}
