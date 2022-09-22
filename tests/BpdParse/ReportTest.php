<?php

namespace BpdParse;

use Balsama\BostonPoliceReportParser\Report;

class ReportTest extends \PHPUnit\Framework\TestCase
{
    private string $exampleReportText;

    public function setUp(): void
    {
        parent::setUp();
        $this->exampleReportText = file_get_contents( __DIR__ . '/../pdfs/exampleReport1');
    }

    public function testInstantiate()
    {
        $report = new Report($this->exampleReportText);
    }

}