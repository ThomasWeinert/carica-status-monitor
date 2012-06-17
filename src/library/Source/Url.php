<?php

namespace Carica\StatusMonitor\Library\Source {

  class Url implements \Carica\StatusMonitor\Library\Source {

    /**
    * @var string
    */
    private $_url = '';

    /**
     * @param string $url
     */
    public function __construct($url) {
      $this->_url = $url;
    }

    /**
     * @return \DOMDocument
     */
    public function read() {
      $xml = file_get_contents($this->_url);
      if (!empty($xml)) {
        $dom = new \DOMDocument();
        $dom->loadXml($xml);
        return $dom;
      }
      return NULL;
    }
  }
}
