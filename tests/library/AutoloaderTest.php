<?php

namespace Carica\StatusMonitor\Library {

  require_once(__DIR__.'/../../src/library/Autoloader.php');

  class AutoloaderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers Autoloader
     * @dataProvider provideFilenameClassPairs
     */
    public function testGetFilename($expected, $class) {
      $this->assertEquals(
        $expected,
        str_replace(DIRECTORY_SEPARATOR, '/', Autoloader::getFileName($class))
      );
    }

    public static function provideFilenameClassPairs() {
      return array(
        array('/Filter/Xslt.php', __NAMESPACE__.'\Filter\Xslt')
      );
    }
  }
}