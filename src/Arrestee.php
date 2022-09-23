<?php

namespace Balsama\BostonPoliceReportParser;

class Arrestee
{
    public string $name;
    public string $address;
    public string $charge;

    const UNKNOWN = 'unknown';

    public function __construct(array $arresteeParts)
    {
        switch (count($arresteeParts)) {
            case 1:
                $this->name = $arresteeParts[0];
                $this->address = self::UNKNOWN;
                $this->charge = self::UNKNOWN;
                break;
            case 2:
                $this->name = $arresteeParts[0];
                $this->address = self::UNKNOWN;
                $this->charge = trim($arresteeParts[1]);
                break;
            case 3:
                $this->name = $arresteeParts[0];
                $this->address = trim($arresteeParts[1]);
                $this->charge = trim($arresteeParts[2]);
                break;
            default:
                break;
        }
    }

}