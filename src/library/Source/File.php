<?php
/**
* Load the DOM from an file
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/
namespace Carica\StatusMonitor\Library\Source {

  /**
   * Load the DOM from an file
   */
  class File implements \Carica\StatusMonitor\Library\Source {

    /**
     * @var string
     */
    private $_file = '';
    /**
    * @var string
    */
    private $_timeout = 2;

    /**
     * @param string $file
     * @param integer|float $timeout
     */
    public function __construct($file, $timeout = 3) {
      $this->setFile($file);
      $this->_timeout = ($timeout > 0 && $timeout < 60) ? $timeout : 3;
    }

    public function setFile($file) {
      $this->_file = $file;
    }

    /**
     * Read the file into a DOMDocument and return it.
     *
     * @return \DOMDocument
     */
    public function read() {
      $dom = new \DOMDocument();
      $dom->load($this->_file);
      return $dom;
    }
  }
}
