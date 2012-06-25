<?php
/**
* Wrapping directory functions into an object
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library {

  /**
   * Wrapping directory functions into an object
   */
  class Directory {

    private $_path = '';

    /**
     * Store the path name the wrapper is for.
     *
     * @param string $filename
     */
    public function __construct($path) {
      $this->_path = $path;
    }

    /**
     * @return string $path
     */
    public function __toString() {
      return $this->_path;
    }

    /**
     * Validate if the file exists
     *
     * @return boolean
     */
    public function exists() {
      return file_exists($this->_path) && is_dir($this->_path);
    }

    public function force() {
      if (!$this->exists()) {
        $oldMask = umask(0);
        if (!mkdir($this->_path, 0777, TRUE)) {
          throw new LogicException(
              'Can not create directory "'.$this->_path.'"'
          );
        }
        umask($oldMask);
      }
    }

  }
}