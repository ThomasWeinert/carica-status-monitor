<?php
/**
* An iterator file the lines of an file resource.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\File {

  use Carica\StatusMonitor\Library as Library;

  /**
   * An iterator file the lines of an file resource.
   */
  class ResourceIterator implements \Iterator {

    /**
     * @var resource
     */
    private $_resource = NULL;

    /**
     * @var integer
     */
    private $_line = -1;

    /**
     * @var FALSE|string
     */
    private $_current = FALSE;

    /**
     * Options bitmask: FILE_IGNORE_NEW_LINES, FILE_SKIP_EMPTY_LINES
     * @var integer
     */
    private $_options = 0;

    /**
     * @param resource $resource
     * @param integer $options FILE_IGNORE_NEW_LINES, FILE_SKIP_EMPTY_LINES
     */
    public function __construct($resource, $options = 0) {
      $this->_resource = $resource;
      $this->_options = $options;
    }

    /**
     * Seek the resource pointer to the start (position 0), set
     * the line variable to -1 and call next to read the first line.
     */
    public function rewind() {
      $options = stream_get_meta_data($this->_resource);
      if ($options['seekable']) {
        fseek($this->_resource, 0);
        $this->_line = -1;
      }
      $this->next();
    }

    /**
     * Read the next line into a buffer, remove the ending linefeed characters
     * and check if it is an empty line.
     *
     * If empty lines are ignored call next() again on an empty line. If new
     * lines are ignored, store the trimmed value in the member variable.
     *
     * Increase the line number variable.
     */
    public function next() {
      if ($this->_line < 0 || $this->_current !== FALSE) {
        $this->_current = fgets($this->_resource);
        if ($this->_current !== FALSE) {
          $trimmedLine = rtrim($this->_current, "\r\n");
          if ($trimmedLine === '' &&
              $this->isIgnoringNewLines()) {
            return $this->next();
          }
          if ($this->isSkippingEmptyLines()) {
            $this->_current = $trimmedLine;
          }
        }
        ++$this->_line;
      }
    }

    public function isIgnoringNewLines() {
      return ($this->_options & FILE_SKIP_EMPTY_LINES) == FILE_SKIP_EMPTY_LINES;
    }

    public function isSkippingEmptyLines() {
      return ($this->_options & FILE_IGNORE_NEW_LINES) == FILE_IGNORE_NEW_LINES;
    }

    /**
     * Return the current line number. Empty lines could be ignored.
     *
     * @return integer
     */
    public function key() {
      return $this->_line;
    }

    /**
     * Return the current line value, the linefeed characters are removed
     * if the option was set.
     *
     * @return string|FALSE
     */
    public function current() {
      return $this->_current;
    }

    /**
     * Validate if the current value is not FALSE
     *
     * @return boolean
     */
    public function valid() {
      return $this->_current !== FALSE;
    }
  }
}