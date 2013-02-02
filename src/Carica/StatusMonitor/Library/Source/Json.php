<?php
/**
* Loads and json from an url and converts it to an xml - with a json
* document element.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Source {

  use Carica\StatusMonitor\Library as Library;

  /**
   * Loads and json from an url and converts it to an xml - with a json
   * document element.
   *
   * For array elements, the parent name is repeated.
   */
  class Json implements Library\Source {

    /**
    * @var string
    */
    private $_url = '';

    /**
    * @var float
    */
    private $_timeout = '';

    /**
    * Add variable type attributes to the element nodes
    * @var string
    */
    public $typeAttributes = FALSE;

    /**
     * @param string $url
     * @param integer|float $timeout
     */
    public function __construct($url, $timeout = 3) {
      $this->_url = $url;
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
        $dom = new \DOMDocument();
        $documentElement = $dom->createElement('json');
        $dom->appendChild($documentElement);
        $this->toDom($documentElement, json_decode($json));
        return $dom;
      }
      return NULL;
    }

    /**
    * Convert a JSON object structure to a DOMDocument
    *
    * @param DOMElement $parentNode
    * @param mixed $current
    * @param integer $maxDepth simple recursion protection
    */
    private function toDom($parentNode, $current, $maxDepth = 100) {
      if (is_array($current) && $maxDepth > 0) {
        foreach ($current as $index => $child) {
          $childNode = $this->addElement($parentNode, $parentNode->tagName);
          $this->toDom($childNode, $child, $maxDepth - 1);
        }
      } elseif (is_object($current) && $maxDepth > 0) {
        foreach (get_object_vars($current) as $index => $child) {
          $childNode = $this->addElement($parentNode, $index);
          $this->toDom($childNode, $child, $maxDepth - 1);
        }
      } elseif (is_bool($current)) {
        $parentNode->appendChild(
          $parentNode->ownerDocument->createTextNode($current ? '1' : '0')
        );
      } elseif (!empty($current)) {
        $parentNode->appendChild(
          $parentNode->ownerDocument->createTextNode((string)$current)
        );
      }
      if ($this->typeAttributes) {
        $parentNode->setAttribute('type', gettype($current));
      }
    }

    /**
    * Add new element, sanitize tag name if nessesary
    *
    * @param DOMElement $parentNode
    * @param string $tagName
    */
    private function addElement($parentNode, $tagName) {
      $nameStartChar =
         'A-Z_a-z'.
         '\\x{C0}-\\x{D6}\\x{D8}-\\x{F6}\\x{F8}-\\x{2FF}\\x{370}-\\x{37D}'.
         '\\x{37F}-\\x{1FFF}\\x{200C}-\\x{200D}\\x{2070}-\\x{218F}'.
         '\\x{2C00}-\\x{2FEF}\\x{3001}-\\x{D7FF}\\x{F900}-\\x{FDCF}'.
         '\\x{FDF0}-\\x{FFFD}\\x{10000}-\\x{EFFFF}';
      $nameChar =
         $nameStartChar.
         '\\.\\d\\x{B7}\\x{300}-\\x{36F}\\x{203F}-\\x{2040}';
      $tagNameNormalized = preg_replace(
        '((^[^'.$nameStartChar.'])|[^'.$nameChar.'])u', '-', $tagName
      );
      $childNode = $parentNode->ownerDocument->createElement($tagNameNormalized);
      if ($tagNameNormalized != $tagName) {
        $childNode->setAttribute('name', $tagName);
      }
      $parentNode->appendChild($childNode);
      return $childNode;
    }
  }
}
