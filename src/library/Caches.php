<?php
/**
* Access to the caching services
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library {

  /**
   * Gives acces to caches by a bucket identiifer.
   *
   * It is possible to register cache configurations, the actual cache
   * service will be created on request.
   *
   * The buckets defines different cache storages.
   *
   * You can request the caches uses the ArrayAccess interface.
   */
  class Caches implements \ArrayAccess {

    private $_caches = array();

    /**
     * Register a cache configuration
     *
     * @param string $bucket
     * @param Cache\Configuration $configuration
     */
    public function register($bucket, Cache\Configuration $configuration) {
      $this->_caches[$bucket] = $configuration;
    }

    /**
     * Set a cache service for an bucket
     *
     * @param string $bucket
     * @param Cache\Service $service
     */
    public function set($bucket, Cache\Service $service) {
      $this->_caches[$bucket] = $service;
    }

    /**
     * Get the cache service for the bucket.
     *
     * If it was only registered before the configuration object will
     * be replaced by the actual cache instance now.
     *
     * @param string $bucket
     * @throws InvalidArgumentException
     * @return Cache\Service
     */
    public function get($bucket) {
      if (!isset($this->_caches[$bucket])) {
        throw new \InvalidArgumentException(
          'Cache bucket not found.'
        );
      } elseif ($this->_caches[$bucket] instanceOf Cache\Configuration) {
        $configuration = $this->_caches[$bucket];
        $service = __NAMESPACE__.'\\'.ucfirst(
          $configuration->get('SERVICE', 'file')
        );
        $this->_caches[$bucket] = new $service($bucket, $configuration);
      }
      return $this->_caches[$bucket];
    }

    /**
     * Validate if a cache for the bucket exists
     *
     * @param string $bucket
     */
    public function offsetExists($bucket) {
      return isset($this->_caches[$bucket]);
    }

    /**
     * Get the cache service for the bucket. This is an alias for
     * the get() method to implement the ArrayAccess interface.
     *
     * @param string $bucket
     * @return Cache\Service
     */
    public function offsetGet($bucket) {
      return $this->get($bucket);
    }

    /**
     * Directly set the cache service for a bucket. This is an alias for
     * the set() method to implement the ArrayAccess interface.
     *
     * @param string $bucket
     * @param Cache\Service $cache
     */
    public function offsetSet($bucket, $cache) {
      return $this->set($bucket, $cache);
    }

    /**
     * Remove the cache for the bucket
     *
     * @param string $bucket
     */
    public function offsetUnset($bucket) {
      if (isset($this->_caches[$bucket])) {
        unset($this->_caches[$bucket]);
      }
    }
  }
}