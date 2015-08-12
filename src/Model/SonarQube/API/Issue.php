<?php

namespace Privalia\SQHeal\Model\SonarQube\API;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Underscore\Underscore as _;

class Issue {

    public function __construct(array $config){
        $this->config = $config;
    }

    public function getViolationCountBySeverity($resourceID){

        $apiURL = '%sapi/resources?resource=%d&metrics=%s&rules=true';

        $baseAddress    = $this->config['baseAddress'];
        $apiUsername    = $this->config['apiUsername'];
        $apiPassword    = $this->config['apiPassword'];

        $issueSeverity = [
            'Blocker'   => 'blocker_violations',
            'Critical'  => 'critical_violations',
            'Major'     => 'major_violations',
            'Minor'     => 'minor_violations',
            'Info'      => 'info_violations',
        ];

        $client = new \GuzzleHttp\Client();
        $status = [];

        foreach($issueSeverity as $name => $key) {
            $apiURLget = sprintf(
                $apiURL,
                $baseAddress,
                $resourceID,
                $key
            );


            $res = $client->get($apiURLget, ['auth' => [$apiUsername, $apiPassword]]);

            if ($res->getStatusCode() != 200) {
                throw new BadRequestHttpException('Error connecting to SonarQube');
            };


            $result = json_decode($res->getBody());

            if(isset(current($result)->msr)) {

                $reduceFunction = function($memo, $item) {
                    return $item->val + $memo;
                };

                $issueCount = _::reduce(current($result)->msr, $reduceFunction, 0);

                $status[] = ['value'=> $issueCount , 'label'=> $name, 'formatted'=> $issueCount ];
            }
        }

        return $status;
    }

    public function getViolationSolvingByDateRange($resourceID, \DateTime $start, \DateTime $end){

        $apiURL = '%sapi/timemachine/index?resource=%d&metrics=%s&fromDateTime=%s&toDateTime=%s';


        $baseAddress    = $this->config['baseAddress'];
        $apiUsername    = $this->config['apiUsername'];
        $apiPassword    = $this->config['apiPassword'];

        $issueSeverity = [
            'Blocker'   => 'blocker_violations',
            'Critical'  => 'critical_violations',
            'Major'     => 'major_violations',
            'Minor'     => 'minor_violations',
            'Info'      => 'info_violations',
        ];

        $client = new \GuzzleHttp\Client();
        $status = [];

        foreach($issueSeverity as $name => $key) {
            $apiURLget = sprintf(
                $apiURL,
                $baseAddress,
                $resourceID,
                $key,
                $start->format(\DateTime::ISO8601),
                $end->format(\DateTime::ISO8601)
            );


            $res = $client->get($apiURLget, ['auth' => [$apiUsername, $apiPassword]]);

            if ($res->getStatusCode() != 200) {
                throw new BadRequestHttpException('Error connecting to SonarQube');
            };


            $result = json_decode($res->getBody());

            if(isset(current($result)->cells)) {

                $reduceFunction = function($memo, $item) use($name) {

                    $dateTimestamp = strtotime($item->d);
                    $weekDayNumber = date('N', $dateTimestamp);

                    if(!isset($memo[$weekDayNumber]) || $memo[$weekDayNumber]['timestamp'] < $dateTimestamp) {
                        $countValue = current($item->v);
                        $memo[$weekDayNumber]= [
                            'datetime'  => $item->d,
                            'timestamp' => $dateTimestamp,
                            'count'     => $countValue,
                            'weekday'   => date('l', $dateTimestamp)
                        ];
                    }

                    return $memo;
                };

                $dateHolder = [];

                $weekDeltas = _::reduce(current($result)->cells, $reduceFunction, $dateHolder);

                $status[$name] = $weekDeltas;
            }
        }

        foreach($status as $serverity => &$weekDay){
            $initialCount = current($weekDay)['count'] . 'xxz';
            foreach ($weekDay as &$day){
                $day['weekDelta'] = $day['count'] - $initialCount;
            }
        }

//        return $status; //@TODO extract the next functionality to a helper method.
        $weekDays = [
            1 => [ 'day' => 'Monday'],
            2 => [ 'day' => 'Tuesday'],
            3 => [ 'day' => 'Wednesday'],
            4 => [ 'day' => 'Thursday'],
            5 => [ 'day' => 'Friday'],
            6 => [ 'day' => 'Saturday'],
            7 => [ 'day' => 'Sunday'],
        ];

        foreach($weekDays as $weekDayNumber => &$resultArray) {
            foreach ($issueSeverity as $severity => $key) {
                if( isset($status[$severity][$weekDayNumber])){
                    $resultArray[$severity] = $status[$severity][$weekDayNumber]['weekDelta'];
                }
            }
        }

        return array_values($weekDays);
    }
}
