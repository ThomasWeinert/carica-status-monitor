<?php

namespace Carica\StatusMonitor\Library\FileSystem {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class DirectoryTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\Directory::__construct
     * @covers Carica\StatusMonitor\Library\FileSystem\Directory::__toString
     */
    public function testConstructor() {
      $directory = new Directory(__DIR__);
      $this->assertSame(
        __DIR__, (string)$directory
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\Directory::exists
     */
    public function testExistsExpectingTrue() {
      $directory = new Directory(__DIR__);
      $this->assertTrue($directory->exists());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\Directory::exists
     */
    public function testExistsExpectingFalse() {
      $directory = new Directory(__DIR__.'INVALID');
      $this->assertFalse($directory->exists());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\Directory::isReadable
     */
    public function testIsReadableExpectingTrue() {
      $directory = new Directory(__DIR__);
      $this->assertTrue($directory->isReadable());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\Directory::isReadable
     */
    public function testIsReadableExpectingFalse() {
      $directory = new Directory(__FILE__.'INVALID');
      $this->assertFalse($directory->isReadable());
    }
  }
}