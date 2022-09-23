<?php

namespace Balsama\BostonPoliceReportParser;

class Officer
{

    public string $fullText;
    public string $employeeNumber;
    public string $name;

    const UNKNOWN = 'unknown';

    public function __construct(string $officerFullText)
    {
        $this->fullText = trim($officerFullText);

        $pattern = '/^[0-9]{6}\s\s[a-zA-Z]/';
        if (preg_match($pattern, $officerFullText)) {
            $employeeNumber = substr($officerFullText, 0, 6);
            $name = trim(substr($officerFullText, 7));
        }
        $this->employeeNumber = $employeeNumber ?? self::UNKNOWN;
        $this->name = $name ?? self::UNKNOWN;
    }

}