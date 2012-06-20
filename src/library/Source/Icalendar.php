<?php
/**
* Load an iCalendar file and convert at into an DOM.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Source {

  use Carica\StatusMonitor\Library as Library;

  /**
* Load an iCalendar file and convert at into an DOM.
   */
  class Icalendar implements Library\Source {

    /**
    * @var string
    */
    private $_url = '';

    /**
     * @var Traversable
     */
    private $_fileIterator = NULL;

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
      $dom = new \DOMDocument();
      $calendarNode = NULL;
      $itemNode = NULL;
      $lineBuffer = '';
      foreach ($this->fileIterator() as $line) {
        $line = rtrim($line, "\r\n");
        $firstChar = substr($line, 0, 1);
        if ($lineBuffer != '' && $firstChar != ' ' && $firstChar != "\t") {
          if ($token = $this->parseLine($lineBuffer)) {
            switch ($token['name']) {
            case 'BEGIN' :
              switch ($token['value']) {
              case 'VCALENDAR' :
                $dom->appendChild(
                  $calendarNode = $dom->createElement('calendar')
                );
                break;
              case 'VEVENT' :
                if (isset($calendarNode)) {
                  $calendarNode->appendChild(
                    $itemNode = $dom->createElement('event')
                  );
                }
                break;
              case 'VTODO' :
                if (isset($calendarNode)) {
                  $calendarNode->appendChild(
                    $itemNode = $dom->createElement('todo')
                  );
                }
                break;
              }
              break;
            case 'END' :
              switch ($token['value']) {
              case 'VCALENDAR' :
                $calendarNode = NULL;
                break;
              case 'VEVENT' :
              case 'VTODO' :
                $itemNode = NULL;
              }
              break;
            default :
              if (isset($itemNode)) {
                $itemNode->appendChild(
                  $dataNode = $dom->createElement('data')
                );
                $dataNode->setAttribute('name', $token['name']);
                $dataNode->appendChild(
                  $valueNode = $dom->createElement('value')
                );
                $valueNode->appendChild(
                  $dom->createTextNode(
                    str_replace(
                      array('\\,', '\\n'),
                      array(',', "\n\n"),
                      $token['value']
                    )
                  )
                );
                if (!empty($token['paramName'])) {
                  $dataNode->appendChild(
                    $paramNode = $dom->createElement('parameter')
                  );
                  $paramNode->setAttribute(
                    'name', $token['paramName']
                  );
                  $paramNode->setAttribute(
                    'value', $token['paramValue']
                  );
                }
              }
            }
          }
          $lineBuffer = '';
        }
        $lineBuffer .= ltrim($line);
      }
      return $dom;
    }

    private function parseLine($line) {
      if (preg_match($this->_linePattern, $line, $parts)) {
        return $parts;
      }
      return FALSE;
    }

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
