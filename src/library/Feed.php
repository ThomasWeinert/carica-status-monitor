<?php
/**
* The feed object combines a source and an optional filter
* to create an feed for the AtomReader
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library {

  /**
   * The feed object combines a source and an optional filter
   * to create an feed for the AtomReader
   */
  class Feed {

    /**
     * @var Source
     */
    private $_source = NULL;

    /**
     * @var Feed
     */
    private $_filter = NULL;

    /**
     * @return Source $source
     * @return Filter|NULL $filter
     */
    public function __construct(Source $source, Filter $filter = NULL) {
      $this->_source = $source;
      $this->_filter = $filter;
    }

    /**
     * Read the data from the source and filter it if an filter object was provided.
     * Return the xml of the created DOMDocument.
     *
     * @return Source $source
     * @return Filter|NULL $filter
     */
    public function __toString() {
      $dom = $this->_source->read();
      if ($dom) {
        if ($this->_filter) {
          $dom = $this->_filter->filter($dom);
        }
      } else {
        $this->status(504, 'Gateway Time-out');
      }
      return ($dom) ? $dom->saveXml() : '';
    }

    /**
     * Change the response status
     * @param integer $code
     * @param string $text
     */
    public function status($code, $text) {
      header('Status: '.$text, TRUE, $code);
    }
  }
}