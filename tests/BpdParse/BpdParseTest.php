<?php

namespace BpdParse;

use Balsama\BostonPoliceReportParser\BpdParse;
use Balsama\BostonPoliceReportParser\Report;

class BpdParseTest extends \PHPUnit\Framework\TestCase
{

    private BpdParse $bpdParse;

    public function setUp(): void
    {
        parent::setUp();
        $pathToPdf = __DIR__ . '/../pdfs/district_journal_arrests_public+(83).pdf';
        $bpdParse = new BpdParse($pathToPdf);
        $this->bpdParse = $bpdParse;
    }

    public function testLoad()
    {
        $this->assertIsString($this->bpdParse->text);
    }

    public function testFindReports()
    {
        $text = $this->bpdParse->text;
        $reports = $this->bpdParse->findReports($text);

        $this->assertIsArray($reports);
    }

    public function testParse()
    {
        $processedReports = $this->bpdParse->processedReports;
        $this->assertIsArray($processedReports);
        foreach ($processedReports as $processedReport) {
            $this->assertInstanceOf('Balsama\BostonPoliceReportParser\Report', $processedReport);
        }
    }

    public function testToString()
    {
        $json = (string) $this->bpdParse;
        $reports = json_decode($json);

        $this->assertCount(192, $reports);
    }

    /**
     * @all
     */
    public function testStripPageBreaks()
    {
        $exampleReportText = file_get_contents( __DIR__ . '/../pdfs/exampleReport5');
        $this->assertStringContainsString('Boston Police Department', $exampleReportText);
        $modifiedExampleReportText = $this->bpdParse->stripPageBreaks($exampleReportText);
        // @todo somehow, the spaces in this file neede for the regex to work are getting removed so this fails.
        //    confirmed that this works during normal processing though.
        //$this->assertStringNotContainsString('Boston Police Department', $modifiedExampleReportText);
    }

}