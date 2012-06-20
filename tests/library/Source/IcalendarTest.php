<?php

namespace Carica\StatusMonitor\Library\Source {

  require_once(__DIR__.'/../TestCase.php');

  use \Carica\StatusMonitor\Library as Library;

  class IcalendarTest extends Library\TestCase {

    /**
     * @covers Icalendar
     */
    public function testReadWithSimpleFile() {
      $source = new Icalendar('');
      $source->fileIterator(
        new \ArrayIterator(
          file(__DIR__.'/TestData/simple.ical')
        )
      );
      $this->assertXmlStringEqualsXmlString(
        '<calendar>
          <event>
            <data name="DTEND">
              <value>20120619T230000Z</value>
              <parameter name="VALUE" value="DATE-TIME"/>
            </data>
            <data name="DTSTART">
              <value>20120619T180000Z</value>
              <parameter name="VALUE" value="DATE-TIME"/>
            </data>
            <data name="DESCRIPTION">
              <value>Sample Description</value>
            </data>
            <data name="URL">
              <value>http://example.tld/</value>
            </data>
            <data name="SUMMARY">
              <value>Example Summary</value>
            </data>
            <data name="LOCATION">
              <value>Example Location</value>
            </data>
          </event>
        </calendar>',
        $source->read()->saveXml()
      );
    }
  }
}