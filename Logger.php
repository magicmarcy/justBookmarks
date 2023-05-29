<?php

class Logger
{
  const FUNCTION         = 'function';
  const SERVER_PHPSELF   = 'PHP_SELF';

  const ENTRY            = 'Entry -> ';
  const EXIT             = 'Exit -> ';

  const LOGLEVEL_TRACE   = 'TRACE';
  const LOGLEVEL_INFO    = 'INFO ';
  const LOGLEVEL_ERROR   = 'ERROR';
  const LOGLEVEL_WARN    = 'WARN ';
  const LOGLEVEL_SQL     = 'SQL  ';

  const SESSION_USERDATA = 'userdata';
  const LOCALHOST_IPV4   = '127.0.0.1';
  const LOCALHOST_IPV6   = '::1';

  public static function trace(string $message): void
  {
    $methodName = debug_backtrace()[1][self::FUNCTION] ?? '';

    if (empty($methodName)) {
      $methodName = $_SERVER[self::SERVER_PHPSELF];
    }
    self::logit(self::formatMethodNameForLogging($methodName) . $message, self::LOGLEVEL_TRACE);
  }

  public static function traceEntry(): void
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . self::ENTRY, self::LOGLEVEL_TRACE);
  }

  public static function traceEntryData(mixed ...$data): void
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . self::ENTRY . json_encode($data), self::LOGLEVEL_TRACE);
  }

  public static function traceExit(): void
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . self::EXIT, self::LOGLEVEL_TRACE);
  }

  public static function traceExitData(mixed $data): mixed
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . self::EXIT . json_encode($data), self::LOGLEVEL_TRACE);

    return $data;
  }

  public static function traceExitDataMsg(string $message, mixed $data): mixed
  {
    if (!empty($message)) {
      $resultMessage = self::EXIT . $message . " " . json_encode($data);
    } else {
      $resultMessage = self::EXIT . json_encode($data);
    }

    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . $resultMessage, self::LOGLEVEL_TRACE);

    return $data;
  }

  public static function info(string $message): void
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . $message, self::LOGLEVEL_INFO);
  }

  public static function error(string $message): void
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . $message, self::LOGLEVEL_ERROR);
  }

  public static function warn(string $message): void
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '') . $message, self::LOGLEVEL_WARN);
  }

  public static function sql(string $sql): void
  {
    self::logit(self::formatMethodNameForLogging(debug_backtrace()[1][self::FUNCTION] ?? '')  . formatString(EXECUTE_SQL, [$sql]), self::LOGLEVEL_SQL);
  }

  public static function logit(string $message, string $level): void
  {
    // falls kein Logverzeichnis existiert
    if (!file_exists(PROJECT_LOGFOLDERNAME)) {
      mkdir(PROJECT_LOGFOLDERNAME, DEFAULT_LOGFOLDER_PERMISSION, true);
    }

    $date = new DateTime();
    $dateStringLogfileName = date_format($date, DATE_PATTERN_YMD);
    $dateStringLogger = date_format($date, DATETIME_PATTERN_YMDHMSS);
    $ip = $_SERVER["REMOTE_ADDR"];
    $logType = 3; // Type 3 = Log in Datei
    $logFile = PROJECT_LOGFOLDERNAME . '/' . $dateStringLogfileName . LOG_FILEEXTENSION;

    if ($ip == self::LOCALHOST_IPV6) {
      $ip = self::LOCALHOST_IPV4;
    }

    $logMessage = $dateStringLogger . ' [' . $level . '] [' . self::usernameAndId() . '] (' . $ip . ') | ' . $message . "\n";

    error_log($logMessage, $logType, $logFile);
  }

  private static function usernameAndId(): String
  {
    $username = STR_EMPTY;
    $userid = STR_EMPTY;

    if (isset($_SESSION)) {
      $userdata = $_SESSION[self::SESSION_USERDATA];
      $username = $userdata[FIELD_NAME];
      $userid = $userdata[FIELD_ID];
    }

    return $userid . '|' . $username;
  }

  public static function formatMethodNameForLogging(string $methodName): string
  {
    $maxCharacter = 38;
    $methodString = $methodName . '()';

    if (strlen($methodString) < $maxCharacter) {
      $resultString = str_pad($methodString, $maxCharacter);
    } else {
      $resultString = substr($methodString, strlen($methodString));
    }

    return '[' . $resultString . '] ';
  }
}

