<?php
function usernameAndId(): String {
  $username = "";
  $userid = "";

  if (isset($_SESSION)) {
    $userdata = $_SESSION['userdata'];
    $username = $userdata['NAME'];
    $userid = $userdata['ID'];
  }

  return $userid . '|' . $username;
}

class Logger {

  static function trace($message) {
    // falls kein Logverzeichnis existiert
    if (!file_exists('log')) {
      mkdir('log', 0777, true);
    }

    $date = new DateTime();
    $dateStringLogfileName = date_format($date, 'Y-m-d');
    $dateStringLogger = date_format($date, 'Y-m-d H:i:s');
    $ip = $_SERVER["REMOTE_ADDR"];
    $log_type = 3; // Type 3 = Log in Datei
    $log_file = 'log/' . $dateStringLogfileName . '.log';

    $log_message = $dateStringLogger . ' [TRACE] [' . usernameAndId() . '] (' . $ip . ') | ' . $message . "\n";

    error_log($log_message, $log_type, $log_file);
  }

  static function error($message) {
    // falls kein Logverzeichnis existiert
    if (!file_exists('log')) {
      mkdir('log', 0777, true);
    }

    $date = new DateTime();
    $dateStringLogfileName = date_format($date, 'Y-m-d');
    $dateStringLogger = date_format($date, 'Y-m-d H:i:s');
    $ip = $_SERVER["REMOTE_ADDR"];
    $log_type = 3; // Type 3 = Log in Datei
    $log_file = 'log/' . $dateStringLogfileName . '.log';

    $log_message = $dateStringLogger . ' [ERROR] [' . usernameAndId() . '] (' . $ip . ') | ' . $message . "\n";

    error_log($log_message, $log_type, $log_file);
  }

  static function warn($message) {
    // falls kein Logverzeichnis existiert
    if (!file_exists('log')) {
      mkdir('log', 0777, true);
    }

    $date = new DateTime();
    $dateStringLogfileName = date_format($date, 'Y-m-d');
    $dateStringLogger = date_format($date, 'Y-m-d H:i:s');
    $ip = $_SERVER["REMOTE_ADDR"];
    $log_type = 3; // Type 3 = Log in Datei
    $log_file = 'log/' . $dateStringLogfileName . '.log';

    $log_message = $dateStringLogger . ' [WARN ] [' . usernameAndId() . '] (' . $ip . ') | ' . $message . "\n";

    error_log($log_message, $log_type, $log_file);
  }

  static function log($message, $level) {
    // falls kein Logverzeichnis existiert
    if (!file_exists('log')) {
      mkdir('log', 0777, true);
    }

    $date = new DateTime();
    $dateStringLogfileName = date_format($date, 'Y-m-d');
    $dateStringLogger = date_format($date, 'Y-m-d H:i:s');
    $ip = $_SERVER["REMOTE_ADDR"];
    $log_type = 3; // Type 3 = Log in Datei
    $log_file = 'log/' . $dateStringLogfileName . '.log';

    $log_message = $dateStringLogger . ' [' . $level . '] [' . usernameAndId() . '] (' . $ip . ') | ' . $message . "\n";

    error_log($log_message, $log_type, $log_file);
  }
}

