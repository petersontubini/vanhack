<?php
namespace App\Repositories;
use App\Helpers\GuzzleHelper;

class UserRepository
{
    private $endpoint = "https://shiftstestapi.firebaseio.com/users.json";

    public function getUsers()
    {
       $guzzle = new GuzzleHelper($this->endpoint, [], "GET");
       $users = $guzzle->sendRequest();

       if (empty($users))
       {
            return collect([]);
       }

       return $users;
    }
}