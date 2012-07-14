<?php
/**
* Loads and json from and url, looks for the specified property, extracts as and loads it
* as html into an dom
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Source {

  use Carica\StatusMonitor\Library as Library;

  /**
    * Loads and json from and url, looks for the specified property, extracts as and loads it
    * as html into an dom
   */
  class JsonHtml implements Library\Source {

    /**
    * @var string
    */
    private $_url = '';

    /**
    * @var string
    */
    private $_propertyName = '';

    /**
     * @param string $url
     * @param string $propertyName
     * @param integer|float $timeout
     */
    public function __construct($url, $propertyName, $timeout = 3) {
      $this->_url = $url;
      $this->_propertyName = $propertyName;
      $this->_timeout = ($timeout > 0 && $timeout < 60) ? $timeout : 3;
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
          'timeout' => 3.0
        )
      );
      $json = @file_get_contents(validateUrl($this->_url), FALSE, stream_context_create($options));
      if (!empty($json)) {
        $html = $this->getPropertyByName($json, $this->_propertyName);
        if (!empty($html)) {
          $dom = new \DOMDocument();
          $dom->loadHtml($html);
          return $dom;
        }
      }
      return NULL;
    }

    /**
     * Explode the propertyname using "." and look for the matching property in the json array
     *
     * @param array $json
     * @param string $name
     */
    private function getPropertyByName($json, $name) {
      $parts = explode('.', $name);
      $current = json_decode($json, TRUE);
      foreach ($parts as $part) {
        if (is_array($current) && isset($current[$part])) {
          $current =& $current[$part];
        } else {
          return FALSE;
        }
      }
      return $current;
    }
  }
}
