<?php

use Source\Src\LocationPublic;

class LocationTest extends PHPUnit_Framework_TestCase
{

    public static $location;

    public static function setUpBeforeClass()
    {
        $media_id = '987728278054679304_144900939';
        self::$location = new LocationPublic($media_id);
    }

    public static function tearDownAfterClass()
    {
        self::$location = null;
    }

    public function testSetGeoPointsIsSuccess()
    {
        $this->assertTrue(self::$location->setGeoPoints());
    }

    public function testSetLocationDataIsSuccess()
    {
        $this->assertTrue(self::$location->setLocationData());
    }

    public function testSetPlacesIsSuccess()
    {
        $this->assertTrue(self::$location->setPlaces());
    }

    public function testCreateClientIsSuccess()
    {
        $this->assertTrue(self::$location->createClient());
    }

    public function testSetCorrectsValuesLatLng()
    {
        $this->assertEquals(self::$location->lat, '48.847102803');
        $this->assertEquals(self::$location->lng, '2.248830131');
    }

    public function testSetCorrectsValuesAddress()
    {
        $this->assertEquals(self::$location->response['location']['address']['locality'], 'Paris');
        $this->assertEquals(self::$location->response['location']['address']['country'], 'France');
        $this->assertEquals(self::$location->response['location']['address']['postal_code'], '75016');
    }

    public function testSetCorrectsValuesPlaces()
    {
        $this->assertContains('ROLAND-GARROS', self::$location->response['location']['near_places']);
    }
}
