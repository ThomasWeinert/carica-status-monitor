<?php

namespace Carica\StatusMonitor\Library\FileSystem {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class FactoryTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\Factory
     */
    public function testGetFile() {
      $factory = new Factory();
      $file = $factory->getFile(__FILE__);
      $this->assertInstanceOf(
        'Carica\StatusMonitor\Library\FileSystem\File', $file
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\Factory
     */
    public function testGetDirectory() {
      $factory = new Factory();
      $directory = $factory->getDirectory(__DIR__);
      $this->assertInstanceOf(
        'Carica\StatusMonitor\Library\FileSystem\Directory', $directory
      );
    }
  }
}