<?php

namespace Source\src;

/**
* Location Public Class
*
* This class extends from the Location class and sets the access modifiers to 'public' in the properties and methods
* that are used in the phpunits tests for the Location class tests.
*
* @package Source
* @author Leo Poroli
*/

class LocationPublic extends Location
{
    
    /**
    * Public access to $lat property from the Location class.
    *
    * @var integer
    * @access public
    */
    public $lat;

    /**
    * Public access to $lng property from the Location class.
    *
    * @var integer
    * @access public
    */
    public $lng;

    /**
    * Public access to $response property from the Location class.
    *
    * @var array
    * @access public
    */
    public $response;

    /**
    * Executes the same method from the parent class, and returns the answer that it obtains.
    *
    * @access public
    * @return bool True If the data is successfully saved.
    */
    public function setGeoPoints()
    {
        return parent::setGeoPoints();
    }

    /**
    * Executes the same method from the parent class, and returns the answer that it obtains.
    *
    * @access public
    * @return bool True If the data is successfully saved.
    */
    public function setLocationData()
    {
        return parent::setLocationData();
    }

    /**
    * Executes the same method from the parent class, and returns the answer that it obtains.
    *
    * @access public
    * @return bool True If the data is successfully saved.
    */
    public function setPlaces()
    {
        return parent::setPlaces();
    }

    /**
    * Executes the same method from the parent class, and returns the answer that it obtains.
    *
    * @access public
    * @return bool True If the object is successfully created.
    */
    public function createClient()
    {
        return parent::createClient();
    }
}
