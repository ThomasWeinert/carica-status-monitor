<?php
/**
* Transform a DOM using an xslt file.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Filter {

  /**
   * Transform a DOM using an xslt file.
   */
  class Xslt implements \Carica\StatusMonitor\Library\Filter {

    /**
     * @var \XSLTProcessor
     */
    private $_processor = NULL;

    /**
     * @var string
     */
    private $_xsltFile = '';

    /**
     * @var string
     */
    private $_xsltParameters = '';

    /**
     * Create the filter object and store the xslt file name
     *
     * @param string $xsltFile
     */
    public function __construct($xsltFile, array $parameters = array()) {
      $this->_xsltFile = $xsltFile;
      $this->_xsltParameters = $parameters;
    }

    /**
     * Apply the xslt to the provided docment and return the result
     *
     * @param \DOMDocument $dom
     * @return \DOMDocument
     */
    public function filter(\DOMDocument $dom) {
      $template = new \DOMDocument();
      $template->load($this->_xsltFile);
      $this->processor()->importStylesheet($template);
      foreach ($this->_xsltParameters as $name => $value) {
        $this->processor()->setParameter('', $name, $value);
      }
      $this->processor()->setParameter('', 'FEED_PATH', dirname($_SERVER['PHP_SELF']).'/');
      return $this->processor()->transformToDoc($dom);
    }

    /**
     * Getter/Settr for the xsl processor - allow to mock it.
     *
     * @param \XSLTProcessor $processor
     * @return \XSLTProcessor
     */
    public function processor(\XSLTProcessor $processor = NULL) {
      if (isset($processor)) {
        $this->_processor = $processor;
      } elseif (NULL == $this->_processor) {
        $this->_processor = new \XSLTProcessor();
      }
      return $this->_processor;
    }
  }
}