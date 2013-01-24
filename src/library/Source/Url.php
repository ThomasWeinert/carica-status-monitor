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
  class Url implements \Carica\StatusMonitor\Library\Source {

    /**
     * @var string
     */
    private $_url = '';
    /**
    * @var string
    */
    private $_timeout = 2;

    /**
     * @param string $url
     * @param integer|float $timeout
     */
    public function __construct($url, $timeout = 3) {
      $this->setUrl($url);
      $this->_timeout = ($timeout > 0 && $timeout < 60) ? $timeout : 3;
    }

    public function setUrl($url) {
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
          'method'=> "GET",
          'timeout' => (float)$this->_timeout
        )
      );
      $xml = file_get_contents(validateUrl($this->_url), FALSE, stream_context_create($options));
      if (!empty($xml)) {
        $dom = new \DOMDocument();
        $dom->loadXml($xml);
        return $dom;
      }
      return NULL;
    }
  }
}
