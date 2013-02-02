<?php
/**
* Cache configuration definition.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright 2012 Thomas Weinert <thomas@weinert.info>
*/

namespace Carica\StatusMonitor\Library\Cache {

  use Carica\StatusMonitor\Library as Library;

  /**
   * Cache configuration definition. Defines the possible cache options
   * and default values.
   */
  class Configuration extends Library\Configuration {

    protected $_options = array(
      'SERVICE' => 'file',
      'PATH' => '/tmp/Carica/CacheMonitor'
    );

  }
}