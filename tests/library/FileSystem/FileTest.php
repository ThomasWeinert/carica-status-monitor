<?php

namespace Carica\StatusMonitor\Library\FileSystem {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class FileTest extends Library\TestCase {

    /**
     * @covers File::exists
     */
    public function testExistsExpectingTrue() {
      $file = new File(__FILE__);
      $this->assertTrue($file->exists());
    }

    /**
     * @covers File::exists
     */
    public function testExistsExpectingFalse() {
      $file = new File(__FILE__.'INVALID');
      $this->assertFalse($file->exists());
    }
  }
}