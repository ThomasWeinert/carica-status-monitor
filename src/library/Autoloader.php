<?php

namespace Carica\StatusMonitor\Library {

  class Autoloader {

    public static function load($class) {
      if ($file = self::getFilename($class)) {
        include(__DIR__.$file);
      }
    }

    public static function getFilename($class) {
      if (0 == strpos($class, __NAMESPACE__)) {
        return str_replace(
          '\\', DIRECTORY_SEPARATOR, substr($class, strlen(__NAMESPACE__))
        ).'.php';
      }
      return FALSE;
    }

    public static function register() {
      spl_autoload_register(array(__CLASS__, 'load'));
    }
  }
}