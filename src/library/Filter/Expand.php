<?php
/**
* Expand selected nodes using xml url data from the dom.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Filter {

  use Carica\StatusMonitor\Library as Library;

  /**
   * Expand selected nodes using xml url data from the dom.
   *
   * It searches all nodes matching $context in the oringal document, uses the
   * $url expression to define a data source for the context and append all nodes from the found
   * detail document to the context.
   */
  class Expand implements \Carica\StatusMonitor\Library\Filter {

    /**
     * @var Carica\StatusMonitor\Library\Source\Url
     */
    private $_source = NULL;

    /**
     * @var string
     */
    private $_contextExpression = '';

    /**
     * @var string
     */
    private $_urlExpression = 'string(.)';

    /**
     * @var array
     */
    private $_namespaces = array();

    /**
     *
     * @param string $context Xpath to define the context to expand
     * @param string $url Xpath to define url inside the context
     * @param array $namespaces
     */
    public function __construct($context, $url = 'string(.)', array $namespaces = array()) {
      $this->_contextExpression = $context;
      $this->_urlExpression = $url;
      $this->_namespaces = $namespaces;
    }

    /**
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    public function filter(\DOMDocument $dom) {
      $xpath = new \DOMXpath($dom);
      foreach ($this->_namespaces as $prefix => $namespace) {
        $xpath->registerNamespace($prefix, $namespace);
      }
      $source = $this->source();
      foreach ($xpath->evaluate($this->_contextExpression, NULL, FALSE) as $context) {
        $source->setUrl($xpath->evaluate($this->_urlExpression, $context, FALSE));
        if ($details = $source->read()) {
          $context->appendChild($dom->importNode($details->documentElement, TRUE));
        }
      }
      return $dom;
    }

    /**
     * Get source url object usable to load the detail data
     *
     * @param \Carica\StatusMonitor\Library\Source\Url $source
     * @return \Carica\StatusMonitor\Library\Source\Url
     */
    public function source(Library\Source\Url $source = NULL) {
      if (isset($source)) {
        $this->_source = $source;
      } elseif (NULL == $this->_source) {
        $this->_source = new Library\Source\Url('about:blank');
      }
      return $this->_source;
    }
  }
}