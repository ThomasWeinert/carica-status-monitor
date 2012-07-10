<?php
/**
* Wrapping file functions into an object
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\FileSystem {

  /**
   * Wrapping file functions into an object
   */
  class File {

    private $_filename = '';

    /**
     * Store the file name the wrapper is for.
     *
     * @param string $filename
     */
    public function __construct($filename) {
      $this->_filename = $filename;
    }

    /**
     * Return the filename
     *
     * @return string $filename
     */
    public function __toString() {
      return $this->_filename;
    }

    /**
     * Validate if the file exists
     *
     * @return boolean
     */
    public function exists() {
      return file_exists($this->_filename) && is_file($this->_filename);
    }

    /**
     * Validate if the file is readable
     *
     * @return boolean
     */
    public function isReadable() {
      return $this->exists($this->_filename) && is_readable($this->_filename);
    }

    /**
     * Validate if the file is writeable
     *
     * @return boolean
     */
    public function isWriteable() {
      return $this->exists($this->_filename) && is_writeable($this->_filename);
    }

    /**
     * Write the content to the file
     *
     * @param string $data
     */
    public function write($data) {
      file_put_contents($this->_filename, $data);
    }

    /**
     * Read the content from the file
     *
     * @return string
     */
    public function read() {
      return file_get_contents($this->_filename);
    }

    /**
     * Delete the file
     *
     * @return string
     */
    public function delete() {
      if ($this->isWriteable()) {
        unlink($this->_filename);
      }
    }

    /**
     * Get the last modification time
     *
     * @return integer
     */
    public function modified() {
      return filemtime($this->_filename);
    }

  }
}