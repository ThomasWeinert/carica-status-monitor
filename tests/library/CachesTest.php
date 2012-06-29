<?php

namespace Carica\StatusMonitor\Library {

  require_once(__DIR__.'/TestCase.php');

  class CachesTest extends TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\Caches::register
     */
    public function testRegister() {
      $configuration = $this->getMock(
        '\Carica\StatusMonitor\Library\Cache\Configuration'
      );
      $caches = new Caches();
      $caches->register('bucket', $configuration);
      $this->assertTrue(isset($caches['bucket']));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Caches::offsetExists
     */
    public function testOffsetExistsExpectingFalse() {
      $caches = new Caches();
      $this->assertFalse(isset($caches['bucket']));

    }

    /**
     * @covers \Carica\StatusMonitor\Library\Caches::offsetExists
     */
    public function testOffsetExistsExpectingTrue() {
      $service = $this->getMock(
        '\Carica\StatusMonitor\Library\Cache\Service'
      );
      $caches = new Caches();
      $caches->set('bucket', $service);
      $this->assertTrue(isset($caches['bucket']));
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Caches::get
     * @covers \Carica\StatusMonitor\Library\Caches::set
     * @covers \Carica\StatusMonitor\Library\Caches::offsetGet
     * @covers \Carica\StatusMonitor\Library\Caches::offsetSet
     */
    public function testOffsetGetAfterSet() {
      $service = $this->getMock(
        '\Carica\StatusMonitor\Library\Cache\Service'
      );
      $caches = new Caches();
      $caches['bucket'] = $service;
      $this->assertSame($service, $caches['bucket']);
    }
  }
}
