<?php

namespace Balsama\BostonPoliceReportParser;

class Incident
{

    public string $fullText;
    public string $category;
    public string $subCategory;

    const UNKNOWN = 'unknown';

    public function __construct(string $fullText)
    {
        $this->fullText = trim($fullText);

        $subCategory = self::UNKNOWN;

        $incidentSubcategorySeparator = ' - ';
        if (str_contains($fullText, $incidentSubcategorySeparator)) {
            $incidentParts = explode($incidentSubcategorySeparator, $fullText);
            $category = $incidentParts[0];
            array_shift($incidentParts);
            $subCategory = implode('; ', $incidentParts);
        }
        else {
            $category = $fullText;
        }

        $this->subCategory = $subCategory;
        $this->category = $category;
    }

}