<?php

namespace Carica\StatusMonitor\Library {

  require_once(__DIR__.'/TestCase.php');

  class ConfigurationTest extends TestCase {

    /**
     * @covers Configuration::offsetExists
     */
    public function testConstructor() {
      $configuration = new Configuration_TestProxy(
        array(
          'STRING' => 'success',
          'INTEGER' => 42
        )
      );
      $this->assertEquals(
          array(
              'STRING' => 'success',
              'INTEGER' => 42
          ),
          iterator_to_array($configuration)
      );
    }

    /**
     * @covers Configuration::offsetExists
     */
    public function testOffsetExistsExpectingTrue() {
      $configuration = new Configuration_TestProxy();
      $this->assertTrue(isset($configuration['STRING']));
    }

    /**
     * @covers Configuration::offsetExists
     */
    public function testOffsetExistsExpectingFalse() {
      $configuration = new Configuration_TestProxy();
      $this->assertFalse(isset($configuration['INVALID_OPTION']));
    }

    /**
     * @covers Configuration::offsetGet
     */
    public function testOffsetGet() {
      $configuration = new Configuration_TestProxy();
      $this->assertEquals('sample', $configuration['STRING']);
    }

    /**
     * @covers Configuration::offsetGet
     */
    public function testOffsetGetReturnsDefault() {
      $configuration = new Configuration_TestProxy();
      $this->assertEquals(
        'default', $configuration[['INVALID_OPTIONS', 'default']]
      );
    }

    /**
     * @covers Configuration::offsetSet
     */
    public function testOffsetSet() {
      $configuration = new Configuration_TestProxy();
      $configuration['STRING'] = 'success';
      $this->assertEquals('success', $configuration['STRING']);
    }

    /**
     * @covers Configuration::offsetSet
     */
    public function testOffsetSetConvertsType() {
      $configuration = new Configuration_TestProxy();
      $configuration['INTEGER'] = '42';
      $this->assertSame(42, $configuration['INTEGER']);
    }

    /**
     * @covers Configuration::offsetSet
     */
    public function testOffsetSetWithInvalidOptionExpectingException() {
      $configuration = new Configuration_TestProxy();
      $this->setExpectedException('InvalidArgumentException');
      $configuration['INVALID_OPTION'] = 'Trigger Exception';
    }

    /**
     * @covers Configuration::assign
     * @covers Configuration::getIterator
     */
    public function testAssign() {
      $configuration = new Configuration_TestProxy();
      $configuration->assign(
        array(
          'STRING' => 'success',
          'INTEGER' => 42,
          'INVALID_OPTION' => 'Ignored'
        )
      );
      $this->assertEquals(
        array(
          'STRING' => 'success',
          'INTEGER' => 42
        ),
        iterator_to_array($configuration)
      );
    }
  }

  class Configuration_TestProxy extends Configuration {

    protected $_options = array(
      'STRING' => 'sample',
      'INTEGER' => 21
    );
  }
}