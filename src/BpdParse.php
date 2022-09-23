<?php

namespace Balsama\BostonPoliceReportParser;

use Smalot\PdfParser\Parser;

class BpdParse
{
    public string $text;
    public array $processedReports;

    public function __construct(string $pathToPdf)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($pathToPdf);
        $this->text = $pdf->getText();
        $reports = $this->findReports($this->text);
        foreach ($reports as $report) {
            $this->processedReports[] = new Report($report);
        }
    }

    public function __toString(): string{
        return json_encode($this->processedReports);
    }

    public function findReports($reportText): array
    {
        $reports = explode('Report Date & Time', $reportText);
        array_shift($reports);

        $i = 0;
        foreach ($reports as $report) {
            $reports[$i] = $this->stripPageBreaks($report);
            $i++;
        }

        return $reports;
    }

    public function stripPageBreaks($text): string
    {
        $pattern = "/\n[0-9]{1,2}\/[0-9]{2}\/[0-9]{4} [0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2} [AaPp][Mm]\n \nBoston Police Department\n/s";
        $fixedText = preg_replace($pattern, '', $text);
        return $fixedText;
    }


}