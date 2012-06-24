<?php
/**
* Caching service interface.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Cache {

  interface Service {

    /**
     * Read a cache element if it is not older then the given expire.
     * @param string $name
     * @param mixed $parameters
     * @param integer $expires Seconds
     */
    function read($name, $parameters, $expires = 0);

    /**
     * Write data to cache
     *
     * @param string $name
     * @param mixed $parameters
     * @param intger $expires
     * @param mixed $data
     */
    function write($name, $parameters, $expires, $data);

    /**
     * Remove/clear a part or the whole cache
     *
     * @param string $name
     * @param mixed $parameters
     */
    function invalidate($name = NULL, $parameters = NULL);

  }
}