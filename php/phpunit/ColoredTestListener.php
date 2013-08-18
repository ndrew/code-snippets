<?php
/**
 * Test listener that displayes tests and testcase names and highlights
 * errors with color. 
 *
 * @author Andrew Sernyak
 */
class ColoredTestListener implements PHPUnit_Framework_TestListener  {

    protected $_colors = array(
        'LIGHT_RED'     => "[1;31m",
        'LIGHT_GREEN'   => "[1;32m",
        'YELLOW'        => "[1;33m",
        'LIGHT_BLUE'    => "[1;34m",
        'MAGENTA'       => "[1;35m",
        'LIGHT_CYAN'    => "[1;36m",
        'WHITE'         => "[1;37m",
        'NORMAL'        => "[0m",
        'BLACK'         => "[0;30m",
        'RED'           => "[0;31m",
        'GREEN'         => "[0;32m",
        'BROWN'         => "[0;33m",
        'BLUE'          => "[0;34m",
        'CYAN'          => "[0;36m",
        'BOLD'          => "[1m",
        'UNDERSCORE'    => "[4m",
        'REVERSE'       => "[7m",
    );


    /**
     * Output colorized text to terminal run
     */
    protected function termcolored($text, $color="NORMAL", $back=false){
        $t = $this->indent.$text;
        $out = $this->_colors["$color"];
        if($out == ""){ $out = "[0m"; }
        if($back){
            return chr(27)."$out$t".chr(27)."[0m";#.chr(27);
        }else{
            echo chr(27)."$out$t".chr(27).chr(27)."[0m";#.chr(27);
        }
    }

    protected $prev_class = "";
    protected $prev_test = "";
    protected $indent = "";


    protected function setIndent($s) {
        $this->indent = $s;
        $GLOBALS['HighlightTestListener.indent'] = strlen($s);
    }


    public function startTest(PHPUnit_Framework_Test $test) {
        if ($this->prev_class != get_class($test)) {
            if (!empty($this->prev_class) ) {
                print("\n");
            }
            $this->termcolored( get_class($test), 'LIGHT_CYAN', false);
            $this->prev_class = get_class($test);
        }

        if ($this->prev_test != $test->getName() ) {
            print("\n");
        }
        $this->setIndent("  ");
        $this->termcolored($test->getName().": ");
    }


    public function endTest(PHPUnit_Framework_Test $test, $time) {
        $statusLine = "";
        $status = $test->getStatus();
        if($status == PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE) {
            $statusLine .= $this->termcolored("Fail", 'RED', true);
        } else if($status == PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED) {
            $statusLine .= $this->termcolored("Skipped", 'YELLOW', true);
        } else if($status == PHPUnit_Runner_BaseTestRunner::STATUS_INCOMPLETE) {
            $statusLine .= $this->termcolored("Incomplete", 'YELLOW', true);
        } else {
            $statusLine .= $this->termcolored("OK", 'LIGHT_GREEN', true);
        }

        $out = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $test->getActualOutput());
        $this->termcolored($out, "MAGENTA", false);

        $reflectionMethod = new ReflectionProperty('PHPUnit_Framework_TestCase', 'output');
        $reflectionMethod->setAccessible(true);
        $res = $reflectionMethod->setValue($test, "");

        $this->termcolored($statusLine. " took $time s");
        $this->setIndent("");
    }

    
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
        $s = "\033[1;37m\033[41m".PHP_EOL;
        print($s."EXCEPTION: ".$test->getName()."\033[0m\n");
        var_dump($e->getTraceAsString());
    }
    
    
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
        $s = "\033[1;37m\033[41m".PHP_EOL;
        print($s."ASSERTION FAILED: ".$test->getName()."\033[0m\n");    
        var_dump($e->getTraceAsString());
    }

    
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {}
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {}

}