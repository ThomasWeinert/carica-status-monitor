<?php
/**
* Load the DOM from an url
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Source {

  /**
   * Load the DOM from an url
   */
  class HtmlPage implements \Carica\StatusMonitor\Library\Source {

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
     * Read the url into a DOMDocument and return it.
     *
     * @return \DOMDocument
     */
    public function read() {
      $options = array(
        'http'=>array(
          'method'=>
            "GET",
          'header'=>
            "Accept-language: de\r\n" .
            "User-Agent: Mozilla/5.0 (Windows NT 6.0; rv:13.0) Gecko/20100101 Firefox/13.0 \r\n"
        )
      );
      $xml = file_get_contents($this->getUrl(), FALSE, stream_context_create($options));
      if (!empty($xml)) {
        $dom = new \DOMDocument();
        $dom->loadHtml($xml);
        return $dom;
      }
      return NULL;
    }

    /**
     * Get the url, validate that it start with http:// oder
     * https://, map http://example.tld/ to the local example files
     * directory.
     *
     * @throws UnexpectedValueException
     * @return string
     */
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
