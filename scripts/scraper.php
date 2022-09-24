<?php
include_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$url = "https://bpdnews.com/"; // Use ?offset=<timestamp> to start from some time in the past.
scrapePdfsFromUrl($url);

function scrapePdfsFromUrl($url, $totalCount = 0)
{
    $client = new Client();
    $response = $client->request('get', $url);
    $bodyStream = $response->getBody();
    $bodyRaw = $bodyStream->getContents();

    $oldPattern = '/"([^"]*)">\s?Click [hH]ere for Public Journal/'; // Prior to ~1634687986288
    preg_match_all($oldPattern, $bodyRaw, $matches);
    $pdfLinks = $matches[1];

    $pattern = '/"([^"]*)">\s?[hH]ere\s?<\/a>\s?for\sPublic\sJournal/';
    preg_match_all($pattern, $bodyRaw, $matches);
    $pdfLinks = array_merge($pdfLinks, $matches[1]);

    $pattern = '/"([^"]*)">\s?[hH]ere\s?<\/a> for the Public Journal/'; // omg
    preg_match_all($pattern, $bodyRaw, $matches);
    $pdfLinks = array_merge($pdfLinks, $matches[1]);

    $pattern = '/"([^"]*)" target="_blank">\s?[hH]ere\s?<\/a>\s?for the Public Journal/'; // ffs
    preg_match_all($pattern, $bodyRaw, $matches);
    $pdfLinks = array_merge($pdfLinks, $matches[1]);

    $count = 0;
    foreach ($pdfLinks as $pdfLink) {
        if ($pdfLink === '_blank') {
            continue;
        }
        $filenameParts = explode('/', $pdfLink);
        $url = 'https://bpdnews.com' . $pdfLink;
        $client = new Client();
        $response = $client->request('get', $url);
        $bodyStream = $response->getBody();
        $pdfBodyRaw = $bodyStream->getContents();
        foreach ($filenameParts as $part) {
            if (str_contains($part, '.pdf')) {
                $filename = $part;
                continue;
            }
        }
        if (!isset($filename)) {
            $filename = md5(time()) . '.pdf';
        }
        file_put_contents(__DIR__ . '/../scraped/' . $filename, $pdfBodyRaw);
        $count++;
    }

    $totalCount = $totalCount + $count;


    $pattern = '/([0-9]{13})" id="nextLink/';
    preg_match($pattern, $bodyRaw, $match);
    $url = 'https://bpdnews.com/?offset=' . $match[1];
    echo "Scraped from $url and saved $count new PDFs for a total of $totalCount PDFs saved." . PHP_EOL;
    $url = str_replace('_blank', '', $url);
    scrapePdfsFromUrl($url, $totalCount);
}

$foo = 21;