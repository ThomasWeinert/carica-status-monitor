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
      $xml = file_get_contents($this->getUrl());
      if (!empty($xml)) {
        $dom = new \DOMDocument();
        $dom->loadXml($xml);
        return $dom;
      }
      return NULL;
    }
    
    private function getUrl() {
      if (!preg_match('(^https?://)', $this->_url)) {
        throw new UnexpectedValueException('Source url is invalid.');
      }
      $exampleUrl = 'http://example.tld/';
      if (0 === strpos($this->_url, $exampleUrl)) {
        return realpath(
          __DIR__.'/../../../tests/files/'.substr($this->_url, strlen($exampleUrl))
        );
      }
      return $this->_url;
    }
  }
}
