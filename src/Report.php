<?php

namespace Balsama\BostonPoliceReportParser;

use Balsama\Fetch;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Report
{
    private array $parts;

    public string $complaintNumber;
    public string $reportDateTime;
    public string $occuranceDateTime;
    public string $officer;
    public string $address;
    public string $neighborhood;

    public function __construct($reportText)
    {
        $this->parts = explode(PHP_EOL, $reportText);
        $this->findFirstRow();
        $this->findLocation($reportText);
    }

    private function findFirstRow(): void
    {
        if ($this->isValidComplaintNumber((string) trim($this->parts[6]))) {
            $indexes = [5, 6, 7, 8];
        }
        elseif ($this->isValidComplaintNumber((string) trim($this->parts[10]))) {
            $indexes = [9, 10, 11, 12];
        }

        $this->complaintNumber = (string) trim($this->parts[$indexes[1]]);
        $this->reportDateTime = date("Y-m-d H:i:s", strtotime((string) trim($this->parts[$indexes[0]])));
        $this->occuranceDateTime = date("Y-m-d H:i:s", strtotime((string) trim($this->parts[$indexes[2]])));
        $this->officer = (string) trim($this->parts[$indexes[3]]);
    }

    private function findLocation(string $reportText): void
    {
        $location = $this->get_string_between($reportText, 'Location of Occurrence', 'Nature of Incident');
        $location = trim(str_replace('Boston Police Department', '', $location));
        $location = str_replace(PHP_EOL, ' ', $location);
        $latLong = $this->lookupLatLong($location);
        if ($latLong) {
            $this->getNeighborhood($latLong);
        }
    }

    private function getNeighborhood(array $latLong)
    {
        $url = 'https://boston-neighborhood-finder.herokuapp.com/';
        $form_params = [
            'form_params' => [
                'lat' => $latLong[1],
                'long' => $latLong[0],
            ]
        ];
        $client = new Client();
        try {
            $response = $client->request('POST', $url, $form_params);
        }
        catch (ClientException $e) {
            return 'Unknown neighborhood';
        }
        return $this->neighborhood = json_decode($response->getBody())->neighborhood;
    }

    private function lookupLatLong($address)
    {
        $mapboxkey = getenv('mapbox');
        if (!$mapboxkey) {
            if (file_exists(__DIR__ . '/../config.yml')) {
                $config = file_get_contents(__DIR__ . '/../config.yml');
                $mapboxkey = str_replace('mapbox: ', '', $config);
            }
        }
        $url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' . $address . '.json?access_token=' . $mapboxkey;
        $response = Fetch::fetch($url);
        foreach ($response->features as $location) {
            if (property_exists($location, 'context')) {
                foreach ($location->context as $context) {
                    if ($context->text === 'Massachusetts') {
                        $this->address = $location->place_name;
                        return $location->center;
                    }
                }
            }

        }
    }

    private function isValidComplaintNumber($complaintNumber): bool
    {
        if (!is_string($complaintNumber)) {
            throw new \Exception('Cant find complaint number.');
        }
        if (strlen($complaintNumber) < 10) {
            return false;
        }

        return true;
    }

    private function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}