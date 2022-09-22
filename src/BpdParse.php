<?php

namespace Balsama\BostonPoliceReportParser;

class BpdParse
{
    public string $text;
    public array $processedReports;

    public function __construct(string $pathToPdf)
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pathToPdf);
        $this->text = $pdf->getText();
        $reports = $this->findReports($this->text);
        foreach ($reports as $report) {
            $this->processedReports[] = new Report($report);
            echo '';
        }
    }

    public function __toString(): string{
        return json_encode($this->processedReports);
    }

    public function findReports($reportText): Array
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

    private function stripPageBreaks($text): string
    {
        $parts = explode('Boston Police Department', $text);
        // @todo: Remove the switch in assign first line.
        return $text;
    }


}