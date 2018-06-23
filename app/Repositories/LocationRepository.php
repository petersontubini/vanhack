<?php
namespace App\Repositories;
use App\Helpers\GuzzleHelper;

class LocationRepository
{
    private $endpoint = "https://shiftstestapi.firebaseio.com/locations.json";

    public function getLocations()
    {
       $guzzle = new GuzzleHelper($this->endpoint, [], "GET");
       $locations = $guzzle->sendRequest();

       if (empty($locations))
       {
            return collect([]);
       }

       return $locations;
    }
}