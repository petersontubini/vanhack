<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Repositories\LocationRepository;
use App\Repositories\TimePunchRepository;
use Carbon\Carbon;

class UserTimeController extends Controller
{
    private $locationRepository, $timePunchRepository, $userRepository, $locations, $users;

    function __construct()
    {
        $this->timePunchRepository = new TimePunchRepository();
        $this->userRepository = new UserRepository();
        $this->users = $this->userRepository->getUsers();
        $this->locationRepository = new LocationRepository();
        $this->locations = $this->locationRepository->getLocations();
    }

    public function getUserTimes()
    {
        
        $times = $this->timePunchRepository->getTimePunches();
        $usersCollection = collect([]);

        if (!empty($times))
        {
            $userTimes = $times->groupBy('userId');

            foreach ($userTimes as $time)
            {
                
                $time = $time->filter(function ($item) {
                    return !empty($item->clockedOut);
                })->map(function ($item) {
                    $location = $this->getPunchLocation($item->locationId);
                    $dailyOvertimeThreshold = $this->getDailyOvertimeThreshold($location);
                    if (empty($location) || empty ($dailyOvertimeThreshold))
                    {
                        throw new \Exception("Location or Labour Settings not found.");
                    }
                    $startTime = Carbon::parse($item->clockedIn);
                    $endTime = Carbon::parse($item->clockedOut);
                    $item->workedMinutes = $endTime->diffInMinutes($startTime);
                    $item->dailyOvertimeMinutes = ($item->workedMinutes > $dailyOvertimeThreshold) ? ($item->workedMinutes - $dailyOvertimeThreshold) : 0;
                    return $item;
                });
        
                $days = $time->groupBy(function($item) {
                    return Carbon::parse($item->clockedIn)->format('d-m-Y');
                })->map(function ($item) {
        
                    $weekTotal = $item->sum('workedMinutes');
                    $location = $this->getPunchLocation($item->first()->locationId);
                    $weeklyOvertimeThreshold = $this->getWeeklyOvertimeThreshold($location);

                    if (empty($location) || empty ($weeklyOvertimeThreshold))
                    {
                        throw new \Exception("Location or Labour Settings not found.");
                    }

                    return [
                        "totalOvertimeMinutes" => ($weekTotal > $weeklyOvertimeThreshold) ? ($weekTotal - $weeklyOvertimeThreshold) : 0,
                        "totalMinutes" => $weekTotal,
                        "day" => $item->first()->clockedIn,
                        "week" => Carbon::parse($item->first()->clockedIn)->format('W')
                    ];
                });
        
                $months = $days->groupBy(function($item) {
                    return Carbon::parse($item['day'])->format('m-Y');
                });
        
                $weeks = $months->map(function ($item) {
                    return $item->groupBy('week')->map(function ($week) {
                        $week["totalOvertimeMinutes"] = $week->sum('totalOvertimeMinutes');
                        $week["totalMinutes"] = $week->sum('totalMinutes');
                        return $week;
                    });
                });
                $user = $this->getUser($time->first()->locationId, $time->first()->userId);
                $totalWeekOverHours = 0;

                foreach ($weeks as $week)
                {
                    $totalWeekOverHours += $week->sum('totalOvertimeMinutes') / 60;
                }

                $usersCollection->push([
                    "user" => $user,
                    "total_hours" => ($time->sum('workedMinutes') / 60),
                    "total_overtime_hours" => ($time->sum('dailyOvertimeMinutes') / 60 ) + $totalWeekOverHours
                ]);
                
            }

        }
        
        return view('users')->with('data', $usersCollection);
    }

    private function getUser(string $locationId, string $userId)
    {
        if (!empty($this->users) && !empty($userId) && !empty($userId))
        {
            $locationUsers = collect($this->users[$locationId]);
            return $locationUsers->where("id", $userId)->first();
        }
        return false;
    }

    private function getPunchLocation(string $locationId)
    {
        if (!empty($this->locations) && !empty($locationId))
        {
            return $this->locations->where("id", $locationId)->first();
        }
        return false;
    }

    private function getDailyOvertimeThreshold($location)
    {
        if (!empty($location) && !empty($location->labourSettings))
        {
            return $location->labourSettings->dailyOvertimeThreshold;
        }
        return false;
    }
    
    private function getWeeklyOvertimeThreshold($location)
    {
        if (!empty($location) && !empty($location->labourSettings))
        {
            return $location->labourSettings->weeklyOvertimeThreshold;
        }
        return false;
    }
}