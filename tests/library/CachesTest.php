<?php

namespace Carica\StatusMonitor\Library {

  require_once(__DIR__.'/TestCase.php');

  class CachesTest extends TestCase {

    /**
     * @covers Caches::register
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
     * @covers Caches::offsetExists
     */
    public function testOffsetExistsExpectingFalse() {
      $caches = new Caches();
      $this->assertFalse(isset($caches['bucket']));

    }

    /**
     * @covers Caches::offsetExists
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
     * @covers Caches::get
     * @covers Caches::set
     * @covers Caches::offsetGet
     * @covers Caches::offsetSet
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
