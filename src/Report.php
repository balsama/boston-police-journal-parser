<?php

namespace Balsama\BostonPoliceReportParser;

class Report
{
    private array $parts;

    public string $complaintNumber;
    public string $reportDateTime;
    public string $occuranceDateTime;
    public Officer $officer;
    public string $officerEmployeeNumber;
    public string $location;
    public Incident $incident;
    public array $arrestees;

    const UNKNOWN = 'unknown';

    public function __construct($reportText)
    {
        $this->parts = explode(PHP_EOL, $reportText);
        $this->findFirstRow();
        $this->findLocation($reportText);
        $this->findIncidentType();
        $this->findArrestees($reportText);
    }

    private function findArrestees(string $reportText)
    {
        $this->arrestees = [];
        foreach ($this->parts as $index => $part) {
            if ($part === 'Arrests') {
                $arrestsStartingIndex = $index;
                continue;
            }
        }

        if (!isset($arrestsStartingIndex)) {
            return;
        }

        $arresteesParts = array_slice($this->parts, $arrestsStartingIndex + 2);
        $arresteesParts = array_filter($arresteesParts, function($item) {
            if (ctype_space($item) || empty($item)) {
                return false;
            }
            return true;
        });
        $arresteesArrays = array_chunk($arresteesParts, 3);

        foreach ($arresteesArrays as $arresteeArray) {
            $this->arrestees[] = new Arrestee($arresteeArray);
        }
    }

    private function findIncidentType(): void
    {
        $incidentTypeFullText = self::UNKNOWN;
        foreach ($this->parts as $index => $part) {
            if ($part === 'Nature of Incident') {
                $incidentIndex = $index + 2;
                $incidentTypeFullText = (string) trim($this->parts[$incidentIndex]);
                continue;
            }
        }

        $this->incident = new Incident($incidentTypeFullText);
    }

    private function findFirstRow(): void
    {
        if ($this->isValidComplaintNumber((string) trim($this->parts[6]))) {
            $indexes = [5, 6, 7, 8];
        }
        elseif (array_key_exists(11, $this->parts)) {
            if ($this->isValidComplaintNumber((string)trim($this->parts[11]))) {
                // @see tests/pdfs/exampleReport3
                $indexes = [10, 11, 12, 13];
            }
            else {
                return;
            }
        }
        else {
            return;
        }

        $this->complaintNumber = (string) trim($this->parts[$indexes[1]]);
        $this->reportDateTime = date("Y-m-d H:i:s", strtotime((string) trim($this->parts[$indexes[0]])));
        $this->occuranceDateTime = date("Y-m-d H:i:s", strtotime((string) trim($this->parts[$indexes[2]])));
        $this->officer = new Officer((string) trim($this->parts[$indexes[3]]) ?: self::UNKNOWN);
    }

    private function findLocation(string $reportText): void
    {
        $location = $this->get_string_between($reportText, 'Location of Occurrence', 'Nature of Incident');
        $location = trim(str_replace('Boston Police Department', '', $location));
        $location = str_replace(PHP_EOL, ' ', $location);
        $this->location = $location;
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