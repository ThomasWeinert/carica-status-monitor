<?php

namespace Carica\StatusMonitor\Library {

  include_once(__DIR__.'/../../../../src/Carica/StatusMonitor/Library/Autoloader.php');

  class AutoloaderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers \Carica\StatusMonitor\Library\Autoloader
     */
    public function testLoadWithInvalidClass() {
      $this->assertFalse(
        Autoloader::load('\stdClass')
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Autoloader
     * @dataProvider provideFilenameClassPairs
     */
    public function testGetFilename($expected, $class) {
      $this->assertEquals(
        $expected,
        str_replace(DIRECTORY_SEPARATOR, '/', Autoloader::getFileName($class))
      );
    }

    /**
     * @covers \Carica\StatusMonitor\Library\Autoloader
     */
    public function testGetFilenameExpectingFalse() {
      $this->assertFalse(
        Autoloader::getFileName('\stdClass')
      );
    }

    public static function provideFilenameClassPairs() {
      return array(
        array('/Filter/Xslt.php', __NAMESPACE__.'\Filter\Xslt'),
        array('/Configuration.php', 'Carica\StatusMonitor\Library\Configuration')
      );
    }
  }
}