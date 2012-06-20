<?php
/**
 * An iterator for the lines of an file specified by the name. Basically an
 * "file" replacement that reads the file during the iteration.
 *
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright 2012 Thomas Weinert <thomas@weinert.info>
 */


namespace Carica\StatusMonitor\Library\File {

  use Carica\StatusMonitor\Library as Library;

  /**
   * An iterator for the lines of an file specified by the name. Basically an
   * "file" replacement that reads the file during the iteration.
   */
  class Iterator implements \IteratorAggregate {

    /**
     * @var string
     */
    private $_name = '';

    /**
     * Options bitmask: FILE_IGNORE_NEW_LINES, FILE_SKIP_EMPTY_LINES
     * @var integer
     */
    private $_options = 0;

    /**
     * @param string $name
     * @param integer $options FILE_IGNORE_NEW_LINES, FILE_SKIP_EMPTY_LINES
     */
    public function __construct($name, $options = 0) {
      $this->_name = $name;
      $this->_options = $options;
    }

    public function getIterator() {
      return new ResourceIterator(fopen($this->_name, 'r'), $this->_options);
    }
  }
}