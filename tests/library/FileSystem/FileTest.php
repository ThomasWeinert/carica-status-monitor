<?php

namespace Carica\StatusMonitor\Library\FileSystem {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class FileTest extends Library\TestCase {

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\File::__construct
     * @covers Carica\StatusMonitor\Library\FileSystem\File::__toString
     */
    public function testConstructor() {
      $file = new File(__FILE__);
      $this->assertSame(
        __FILE__, (string)$file
      );
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\File::exists
     */
    public function testExistsExpectingTrue() {
      $file = new File(__FILE__);
      $this->assertTrue($file->exists());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\File::exists
     */
    public function testExistsExpectingFalse() {
      $file = new File(__FILE__.'INVALID');
      $this->assertFalse($file->exists());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\File::isReadable
     */
    public function testIsReadableExpectingTrue() {
      $file = new File(__FILE__);
      $this->assertTrue($file->isReadable());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\File::isReadable
     */
    public function testIsReadableExpectingFalse() {
      $file = new File(__FILE__.'INVALID');
      $this->assertFalse($file->isReadable());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\File::isWriteable
     */
    public function testIsWriteableExpectingFalse() {
      $file = new File(__FILE__.'INVALID');
      $this->assertFalse($file->isWriteable());
    }

    /**
     * @covers Carica\StatusMonitor\Library\FileSystem\File::read
     */
    public function testRead() {
      $file = new File(__DIR__.'/TestData/sample.txt');
      $this->assertEquals('success', $file->read());
    }
  }
}