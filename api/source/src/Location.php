<?php

namespace Source\src;

use Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
* Location Class
*
* This is the main class. When it is instanced, this executes the constructor method with the
* media-id and filters parameters.
* This contains methods for setting the geopoints data, location data and near places.
* This also contains a method for returning all location data.
*
* @package Source
* @author Leo Poroli
*/

class Location
{
    /**
    * String with Instagram media-id. It is obtained from the URL of the request.
    * @var string
    * @access protected
    */
    protected $media_id;
    
    /**
    * Latitud coordinate. For Geopoint
    * @var integer
    * @access protected
    */
    protected $lat;

    /**
    * Longitude coordinate. For Geopoint
    * @var integer
    * @access protected
    */
    protected $lng;

    /**
    * This contains all the data location response.
    * @var array
    * @access protected
    */
    protected $response = array();

    /**
    * Object from de Guzzle Class. To make http queries.
    * @var object
    * @access protected
    */
    protected $client;

    /**
     * Contruct method
     *
     * This receives two parameters of media-id and filters.
     * The media-id parameter is assigned to media-id property.
     * The default filters parameter is null.
     *
     * This executes the three setter methods if the filters parameter is null.
     * First it executes setGeoPoint; if this setter method does not fail, then it executes the other two
     * (setLocationData and setPlaces)
     *
     * The setLocationData and setPlaces methods are executed in order or not, depending on whether they are
     * present in the filters parameter.
     *
     * @access public
     * @param string $par1 The media-id from Instagram
     * @param string $par2 The filters.
     */
    public function __construct($par1, $par2 = null)
    {
        $this->media_id = $par1;
        if ($par2 == null) {
            if (!empty($this->media_id)) {
                if ($this->setGeoPoints()) {
                    $this->setLocationData();
                    $this->setPlaces();
                }
            }
        } else {
            //If the filter parameter is received. These filters are saved in an array.
            $filters = explode(',', $par2);
            if (!empty($this->media_id)) {
                if ($this->setGeoPoints()) {
                    //Loops through the array and executes the corresponding methods.
                    foreach ($filters as $filter) {
                        if ($filter == 'address') {
                            $this->setLocationData();
                        } elseif ($filter == 'places') {
                            $this->setPlaces();
                        }
                    }
                }
            }
        }
    }

    /**
    * Sets the Geo Points data
    *
    * This method gets a response from the resInstagram method with the geopoint data that is obtained from the
    * Instagram API (id, name, latitude and longitude) and saves this data in the response property.
    *
    * @access protected
    * @return bool True if the geopoint data is successfully obtained.
    */
    protected function setGeoPoints()
    {
        //Executes the resInstagram function and saves the result.
        $resInstagram = $this->resInstagram('media');

        //If $resInstagram is an array, then it has the geopoint data.
        if (is_array($resInstagram)) {
            $this->response['media_id'] = $this->media_id;
            if (isset($resInstagram['data']['location'])) {
                //The geopoint data is saved in response property.
                $this->response['location']['id'] = $resInstagram['data']['location']['id'];
                $this->response['location']['name'] = $resInstagram['data']['location']['name'];
                $this->lat = $this->response['location']['geopoint']['latitude'] = $resInstagram['data']['location']['latitude'];
                $this->lng = $this->response['location']['geopoint']['longitude'] =$resInstagram['data']['location']['longitude'];
            } else {
                $this->response['location']['message'] = 'Does not have the data.';
            }
        }

        if (!empty($this->lat) && !empty($this->lng)) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Sets the Location data
    *
    * This method makes a query to the GMAP API with a specific point of latitude and longitude.
    * The GMAP API returns the specific data with the location address.
    * The location address is saved in the response property.
    *
    * @access protected
    * @throws RequestException. If the http client request fails.
    * @return bool True. If the data is successfully saved.
    */
    protected function setLocationData()
    {
        
        //Creates the http client.
        $this->createClient(Config::GMAP_URL);

        //Configures the http client request.
        $uri = 'geocode/json';
        $query['latlng'] = $this->lat.','.$this->lng;
        $query['sensor'] = 'true_or_false';

        try {
            //Executes the http client request and obtains the response.
            $response = $this->client->get($uri, [
                'query' => $query,
                'base_uri' => Config::GMAP_URL,
            ]);
            $maps_json = $response->getBody();
            //Converts the response into an array.
            $maps_array = json_decode($maps_json, true);
            $maps_array_f = $maps_array['results'][0]['address_components'];

            //Saves the address data in the response property.
            for ($i = 0; $i < count($maps_array_f); $i++) {
                $key = $maps_array_f[$i]['types'][0];
                $this->response['location']['address'][$key] = $maps_array_f[$i]['long_name'];
            }

            return true;

        } catch (RequestException $e) {
            //If the http request fails, it obtains and saves the status code and reason phrase.
            if ($e->hasResponse()) {
                $exc_response = $e->getResponse();
                $status_code = $exc_response->getStatusCode();
                $reason_phrase =$exc_response->getReasonPhrase();
                $response = array(
                    'reason_phrase' => $reason_phrase,
                    'satus_code' => $status_code,
                );
            } else {
                //Even if the error response is not present, it sets the status code.
                $response = array(
                    'satus_code' => 400,
                );
            }
            $this->response = $response;

            return false;
        }

    }

    /**
    * Set Places
    *
    * This method executes the resInstagram method with the parameter as 'location.
    * Obtains an array with all the near places at the given parameter.
    * These places are saved in the response property.
    *
    * @access protected
    * @return bool True If the data is successfully saved.
    */
    protected function setPlaces()
    {
        //Executes the resInstagram function and saves the result.
        $resInstagram = $this->resInstagram('locations');
        $instagram_array_f = $resInstagram['data'];

        //The places data is saved in response property.
        for ($i = 0; $i < count($instagram_array_f); $i++) {
            $this->response['location']['near_places'][$i] = $instagram_array_f[$i]['name'];
        }

        if (count($this->response['location']['near_places']) == count($instagram_array_f)) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Instagram API Response
    *
    * This method makes a query to the Instagram API. The query can be executed to get the geopoint data
    * or near places, depending on the assigned parameter.
    * Returns an array with obtained data from de Instagram query.
    * If the http client request fails, the error obtained is saved in the response property.
    *
    * @access protected
    * @param string $type Specifies the type of query (it can be media or locations).
    * @throws RequestException If the http client request fails.
    * @return mixed An array in case the answer to the query is successful.
    */
    protected function resInstagram($type)
    {
        //Configures the query depending on the $type.
        if ($type == 'media') {
            $uri = 'media/'.$this->media_id;
        } elseif ($type == 'locations') {
            $uri = 'locations/search';
            $query['lat'] = $this->lat;
            $query['lng'] = $this->lng;
        }

        //Sets the Instragram access token.
        $query['access_token'] =  Config::INSTAGRAM_TOKEN;

        $this->createClient();

        try {
            //Executes the http client request and obtains the response.
            $response = $this->client->get($uri, [
                'query' => $query,
                'verify' => false,
                'base_uri' => Config::INSTAGRAM_URL,
            ]);
            $instagram_json = $response->getBody();
            $instagram_array = json_decode($instagram_json, true);

            //Returns the response in an array.
            return $instagram_array;

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                //If the http request fails, it obtains and saves the status code and reason phrase.
                $exc_response = $e->getResponse();
                $status_code = $exc_response->getStatusCode();
                $reason_phrase =$exc_response->getReasonPhrase();
                $response = array(
                    'reason_phrase' => $reason_phrase,
                    'satus_code' => $status_code,
                );
            } else {
                //Even if the error response is not present, it sets the status code.
                $response = array(
                    'satus_code' => 400,
                );
            }
            $this->response = $response;
            return 0;
        }
    }

    /**
    * Creates an Http Guzzle Client Object.
    *
    * This method creates and sets the Http Client Object, in case it has not been created yet.
    * The object is saved in the client property.
    *
    * @access protected
    * @return bool True If the object is successfully created.
    */
    protected function createClient()
    {
        //Creates the http client and saves it in the client property, in case it has not been created yet.
        if (!isset($this->client)) {
            $this->client = new Client([
                'headers' => [
                    'Accept' => 'application/json',
                    ]
            ]);
        }

        if (get_class($this->client) == 'GuzzleHttp\Client') {
            return true;
        } else {
            return false;
        }

    }

    /**
    * Returns the response property
    *
    * This method returns the response property. The reponse property contains all the
    * location data obtained from the API requests.
    *
    * @access public
    * @return array With either the geopoint, address and locations data, or the error message.
    */
    public function getResponse()
    {
        return $this->response;
    }
}
