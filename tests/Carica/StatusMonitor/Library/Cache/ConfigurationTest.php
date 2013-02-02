<?php

namespace Carica\StatusMonitor\Library\Cache {

  require_once(__DIR__.'/../TestCase.php');

  use Carica\StatusMonitor\Library as Library;

  class ConfigurationTest extends Library\TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\Cache\Configuration
     */
    public function testDefinition() {
      $options = new Configuration();
      $this->assertEquals(
        array(
         'SERVICE' => 'file',
         'PATH' => '/tmp/Carica/CacheMonitor'
        ),
        iterator_to_array($options)
      );
    }
  }
}