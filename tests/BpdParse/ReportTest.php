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

    public function testConstruct()
    {
        $report = new Report($this->exampleReportText);
        $this->assertInstanceOf('Balsama\BostonPoliceReportParser\Report', $report);
    }

    public function testWeirdReportFormat()
    {
        $exampleReportText = file_get_contents( __DIR__ . '/../pdfs/exampleReport3');
        $report = new Report($exampleReportText);
        $this->assertEquals('222072412-00', $report->complaintNumber);
        $this->assertEquals('5 DUVAL ST', $report->location);
    }

    public function testReportWithMultipleArrests()
    {
        $exampleReportText = file_get_contents( __DIR__ . '/../pdfs/exampleReport4');
        $report = new Report($exampleReportText);
        $this->assertCount(3, $report->arrestees);
        foreach ($report->arrestees as $arrestee) {
            $this->assertInstanceOf('Balsama\BostonPoliceReportParser\Arrestee', $arrestee);
        }
    }

}