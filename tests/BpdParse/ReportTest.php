<?php

namespace BpdParse;

use Balsama\BostonPoliceReportParser\Report;

class ReportTest extends \PHPUnit\Framework\TestCase
{
    private string $exampleReportText;

    public function setUp(): void
    {
        parent::setUp();
        $this->exampleReportText = file_get_contents( __DIR__ . '/../pdfs/exampleReport6');
    }

    public function testIncident()
    {
        $report = new Report($this->exampleReportText);
        $this->assertInstanceOf('Balsama\BostonPoliceReportParser\Report', $report);
        $this->assertEquals("VAL - VIOLATION OF AUTO LAW", $report->incident->fullText);
    }
    public function testIncidentNumber()
    {
        $report = new Report($this->exampleReportText);
        $foo = 21;
        $this->assertEquals("INCIDENTNUMBER", $report->INCIDENTNUMBER);
    }

    public function testComplaintNumber()
    {
        $report = new Report($this->exampleReportText);
        $this->assertTrue(true);
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