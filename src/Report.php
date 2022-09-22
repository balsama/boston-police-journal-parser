<?php

namespace Balsama\BostonPoliceReportParser;

class Report
{
    private array $parts;

    public string $complaintNumber;
    public string $reportDateTime;
    public string $occuranceDateTime;
    public string $officer;

    public function __construct($reportText)
    {
        $this->parts = explode(PHP_EOL, $reportText);
        $this->findFirstRow();
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
}