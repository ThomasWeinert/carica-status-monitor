<?php
/**
 * Data source aggregating several other atom feeds into one
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Source {

  use Carica\StatusMonitor\Library as Library;

  class Aggregate implements Library\Source {

    /**
     * List of feeds to aggregate
     * @var array(\Carica\StatusMonitor\Library\Feed)
     */
    private $_feeds = array();

    /**
     * the atom namespace urn
     * @var string
     */
    private $_xmlns = 'http://www.w3.org/2005/Atom';

    /**
     * store url and optional xslt template
     *
     * @param array $feeds
     */
    public function __construct(array $feeds = array()) {
      foreach ($feeds as $feed) {
        $this->attachFeed($feed);
      }
    }

    /**
     * Store a feed in the internal list. Duplicate feed objects are replaced.
     *
     * @param Library\Feed $feed
     */
    public function attachFeed(Library\Feed $feed) {
      $this->_feeds[spl_object_hash($feed)] = $feed;
    }

    /**
     * Read all feeds and aggregate the entries.
     *
     * @param Library\Feed $feed
     */
    public function read() {
      $result = new \DOMDocument();
      $result->appendChild(
        $feedNode = $this->createAtomElement($result, 'feed')
      );
      foreach ($this->_feeds as $feed) {
        $xpath = new \DOMXpath($subFeed = $feed->get());
        $xpath->registerNamespace('atom', $this->_xmlns);
        foreach ($xpath->evaluate('atom:entry', NULL, FALSE) as $entryNode) {
          $feedNode->appendChild($result->importNode($entryNode, TRUE));
        }
      }
      return $result;
    }

    /**
     * Create a specified atom element, add attribute and text content
     *
     * @param DOMDocument $dom
     * @param string $name
     * @param array $attributes
     * @param string $content
     * @return DOMElement
     */
    public function createAtomElement($dom, $name, array $attributes = array(), $content = '') {
      $node = $dom->createElementNs($this->_xmlns, 'atom:'.$name);
      foreach ($attributes as $attributeName => $attributeValue) {
        $node->setAttribute($name, $value);
      }
      if (!empty($content)) {
        $node->appendElement($dom->createTextNode($content));
      }
      return $node;
    }
  }
}