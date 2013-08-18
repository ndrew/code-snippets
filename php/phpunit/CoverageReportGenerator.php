<?php
// Usage:
//  phpunit -d memory_limit=2048M coverage_report $PATH_TO_SERIALIZED_COVERAGE #> $COVERAGE_LOG

/**
 * Coverage text report generator for serialized php coverages
 *
 * @author Andrew Sernyak
 */
class CoverageReportGenerator_Test extends PHPUnit_Framework_TestCase {


    function setUp() { ; }
    function tearDown() { ; }


    function report($out, $coverage, $showUncoveredFiles = false) {
        $title=$out;
        $lowUpperBound=0;
        $highLowerBound=70;
        $use_colors = true;

        $text_report = new PHP_CodeCoverage_Report_Text( new PHPUnit_Util_Printer($out), $title, $lowUpperBound, $highLowerBound, $showUncoveredFiles );
        echo "\nProcessing $out coverage...";
        $text_report->process($coverage, $use_colors);
        echo ".done\n";
    }


    function test_generate_coverage() {
        global $argv;
        $raw_coverage = null;
        for ($i=sizeof($argv)-1; $i >= 2 ; $i--) { 
            $f = $argv[$i];
            if (strrpos($f, '.serialized', 0) === strlen($f) - strlen('.serialized')) {
                $raw_coverage = $f; 
                break;
            }
        }

        if (null == $raw_coverage) {
            $this->assertFalse(true, "no serialized coverage were provided");
        }

        $coverage = unserialize(file_get_contents($raw_coverage));
        $this->report( str_replace('.serialized','.txt',$raw_coverage), $coverage);
        $this->assertFalse(empty($coverage)); 
    }
}