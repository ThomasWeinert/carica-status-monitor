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
     * @var string
     */
    private $_contentType = 'text/xml';

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
     * Return the the created DOMDocument.
     *
     * @return Source $source
     * @return Filter|NULL $filter
     */
    public function get() {
      if (!($source = $this->source())) {
        throw new LogixException('No datasource defined.');
      }
      $dom = $source->read();
      if ($dom && $filter = $this->filter()) {
        $dom = $filter->filter($dom);
      }
      return $dom;
    }
    
    /**
     * Get the feed result and output it. Send HTTP headers, too.
     *
     * @return Source $source
     * @return Filter|NULL $filter
     */
    public function output() {
      if ($dom = $this->get()) {
        $this->sendContentType();
        echo $dom->saveXml();
      } else {
        $this->status(504, 'Gateway Time-out');
      }
    }

    /**
     * Getter/Setter for the source object, calls createSource if
     * source is not set.
     *
     * @param Library\Source $source
     */
    public function source(Library\Source $source = NULL) {
      if (isset($source)) {
        $this->_source = $source;
      } elseif (NULL === $this->_source) {
        $this->_source = $this->createSource();
      }
      return $this->_source;
    }

    /**
     * Implicit create for the source object, can be overloaded in
     * child classes
     *
     * @return FALSE|Library\Source
     */
    protected function createSource() {
      return FALSE;
    }

    /**
     * Getter/Setter for the filter object
     *
     * @param Library\Filter $filter
     */
    public function filter(Library\Filter $filter = NULL) {
      if (isset($filter)) {
        $this->_filter = $filter;
      } elseif (NULL === $this->_filter) {
        $this->_filter = $this->createFilter();
      }
      return $this->_filter;
    }

    /**
     * Implicit create for the source object, can be overloaded in
     * child classes
     *
     * @return FALSE|Library\Filter
     */
    protected function createFilter() {
      return FALSE;
    }

    /**
     * Change the response status
     * @param integer $code
     * @param string $text
     */
    public function status($code, $text) {
      header('Status: '.$text, TRUE, $code);
    }

    /**
     * Send response content type
     * @param integer $code
     * @param string $text
     */
    public function sendContentType() {
      if (!headers_sent()) {
        header('Content-Type: '.$this->_contentType.'; charset=utf-8');
      }
    }
  }
}