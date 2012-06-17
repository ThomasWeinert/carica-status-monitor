<?php

namespace Carica\StatusMonitor\Library {

  class Autoloader {

    public static function load($class) {
      if ($file = self::getFilename($class)) {
        include(__DIR__.$file);
      }
    }

    public static function getFilename($class) {
      $baseNamespace = substr(__NAMESPACE__, 0, strrpos(__NAMESPACE__, '\\'));
      if (0 == strpos($class, $baseNamespace)) {
        return str_replace(
          '\\', DIRECTORY_SEPARATOR, substr($class, strlen($baseNamespace))
        ).'.php';
      }
      return FALSE;
    }

    public static function register() {
      spl_autoload_register(array(self, 'load'));
    }
  }
}