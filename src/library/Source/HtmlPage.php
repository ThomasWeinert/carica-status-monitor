<?php
/**
* Load the DOM from an html page url, play browser
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Source {

  /**
* Load the DOM from an html page url, play browser
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
          'method'=> "GET",
          'user_agent' => "User-Agent: Mozilla/5.0 (Windows NT 6.0; rv:13.0) Gecko/20100101 Firefox/13.0",
          'timeout' => 3.0
        )
      );
      $xml = @file_get_contents(validateUrl($this->_url), FALSE, stream_context_create($options));
      if (!empty($xml)) {
        $dom = new \DOMDocument();
        $dom->loadHtml($xml);
        return $dom;
      }
      return NULL;
    }
  }
}
