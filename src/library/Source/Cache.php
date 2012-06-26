<?php
/**
* Caches the result of another source for the given time
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/
namespace Carica\StatusMonitor\Library\Source {

  use Carica\StatusMonitor\Library as Library;

  /**
   * Caches the result of another source for the given time
   */
  class Cache implements Library\Source {

    /**
     * @var Libary\Caches\Service
     */
    private $_cache = '';

    /**
     * @var Library\Source
     */
    private $_source = '';

    /**
     * @var string
     */
    private $_identifier = '';

    /**
     * @var integer
     */
    private $_expires = 0;

    /**
     * @param Libary\Caches\Service $cache
     * @param Library\Source $url
     */
    public function __construct(
      Libary\Cache\Service $cache,
      Library\Source $source,
      $identifier,
      $expires = 60
    ) {
      $this->_cache = $cache;
      $this->_source = $source;
      $this->_identifier = $identifier;
      $this->_expires = (int)$expires;
    }

    /**
     * Read the url into a DOMDocument and return it.
     *
     * @return \DOMDocument
     */
    public function read() {
      if ($this->_expires > 0) {
        $name = preg__replace(
          '([^A-Za-z\d]+)', '_', get_class($this->_source)
        );
        $cacheData = $this->_cache->read(
          $name, $this->_identifier, $this->_expires
        );
        if ($cacheData) {
          $dom = new \DOMDocument();
          $dom->loadXml($cacheData);
          return $dom;
        } else {
          $dom = $this->_source->read();
          $this->_cache->write(
            $name, $this->_identifier, $this->_expires, $dom->saveXml()
          );
          return $dom;
        }
      }
      return NULL;
    }
  }
}
