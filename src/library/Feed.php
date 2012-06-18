<?php

namespace Carica\StatusMonitor\Library {

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
      if ($dom && $this->_filter) {
        $dom = $this->_filter->filter($dom);
      }
      return ($dom === NULL) ? '' :  $dom->saveXml();
    }

  }
}