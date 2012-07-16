<?php
/**
* Load an iCalendar file and convert at into an xCalendar DOM.
*
* http://tools.ietf.org/html/draft-royer-calsch-xcal-03
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Source {

  use Carica\StatusMonitor\Library as Library;

  /**
   * Load an iCalendar file and convert at into an xCalendar DOM.
   *
   * http://tools.ietf.org/html/draft-royer-calsch-xcal-03
   */
  class Icalendar implements Library\Source {

    /**
    * @var string
    */
    private $_url = '';

    /**
     * @var string
     */
    private $_xmlns = 'urn:ietf:params:xml:ns:xcal';

    /**
     * @var DOMDocument
     */
    private $_document = NULL;

    /**
     * @var DOMElement
     */
    private $_currentNode = NULL;

    /**
     * @var Traversable
     */
    private $_fileIterator = NULL;

    /**
     * @var string
     */
    private $_linePattern = "(
        (?P<name>[A-Z\d-]+)
        (?:
          ;
          (?P<paramName>[A-Z\d-]+)
          =
          (?P<paramValue>[^:]+)
        )?
        :
        (?P<value>.+)
      )x";

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
      $this->_document = new \DOMDocument();
      $this->_document->appendChild(
        $this->_currentNode = $this->_document->createElementNS(
          $this->_xmlns, 'xCal:iCalendar'
        )
      );
      $lineBuffer = '';
      foreach ($this->fileIterator() as $line) {
        $line = rtrim($line, "\r\n");
        $firstChar = substr($line, 0, 1);
        if ($lineBuffer != '' && $firstChar != ' ' && $firstChar != "\t") {
          if ($token = $this->parseLine($lineBuffer)) {
            $this->appendToken($token);
          }
          $lineBuffer = '';
        }
        $lineBuffer .= ltrim($line);
      }
      if ($lineBuffer != '' && ($token = $this->parseLine($lineBuffer))) {
        $this->appendToken($token);
      }
      return $this->_document;
    }

    /**
     * Append the token data to the dom, If the name is BEGIN an new group element ist created
     * and set as the current element. END switches the current element to its parent
     *
     * All other tokens are appended as with ther name in lowecase as element name.
     * Parameters are converted to attributes.
     *
     * @param array $token
     */

    private function appendToken(array $token) {
      switch ($token['name']) {
      case 'BEGIN' :
        $this->_currentNode->appendChild(
          $groupNode = $this->createXcalElement($token['value'])
        );
        $this->_currentNode = $groupNode;
        break;
      case 'END' :
        $this->_currentNode = $this->_currentNode->parentNode;
        break;
      default :
        $this->_currentNode->appendChild(
          $itemNode = $this->createXcalElement($token['name'])
        );
        $itemNode->appendChild(
          $this->_document->createTextNode(
            str_replace(
              array('\\,', '\\n'),
              array(',', "\n\n"),
              $token['value']
            )
          )
        );
        if (!empty($token['paramName'])) {
          $itemNode->setAttribute(
            strtolower($token['paramName']), $token['paramValue']
          );
        }
      }
    }

    /**
     * Create a DOMElement in the xcal namespace
     *
     * @param string $name
     * @return DOMElement
     */
    private function createXcalElement($name) {
      return $this->_document->createElementNS(
        $this->_xmlns, 'xCal:'.strtolower($name)
      );
    }

    /**
     * Parse the token line using a PCRE
     *
     * @param string $line
     * @return array|FALSE
     */
    private function parseLine($line) {
      if (preg_match($this->_linePattern, $line, $parts)) {
        return $parts;
      }
      return FALSE;
    }

    /**
     * Getter/Setter for the line iterator including an implicit create for
     * a file iterator using the stored url.
     *
     * @param $iterator
     */
    public function fileIterator(\Traversable $iterator = NULL) {
      if (isset($iterator)) {
        $this->_fileIterator = $iterator;
      } elseif (NULL === $this->_fileIterator) {
        $this->_fileIterator = new Library\File\Iterator(
          validateUrl($this->_url),
          FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );
      }
      return $this->_fileIterator;
    }
  }
}
