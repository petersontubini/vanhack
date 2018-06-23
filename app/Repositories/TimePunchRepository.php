<?php
namespace App\Repositories;
use App\Helpers\GuzzleHelper;

class TimePunchRepository
{
    private $endpoint = "https://shiftstestapi.firebaseio.com/timePunches.json";

    public function getTimePunches()
    {
       $guzzle = new GuzzleHelper($this->endpoint, [], "GET");
       $timePunches = $guzzle->sendRequest();

       if (empty($timePunches))
       {
            return collect([]);
       }

       return $timePunches;
    }
}