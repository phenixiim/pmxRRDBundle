<?php
/**
 * Created by JetBrains PhpStorm.
 * @author: pomaxa none <pomaxa@gmail.com>
 * @date: 10/19/12
 */
namespace Pmx\Bundle\RrdBundle\Component;

use RuntimeException;

class PmxRrdGraph
{

    public $filename;

    function getDataSourceFromDb($filename)
    {

        $database = $this->rrdinfo($filename);
        return $database['ds'];
        return array_keys($this->rrdinfo($filename));
    }

    public $title = 'pmx graph example';
    public $verticalLabel = 'This is MyData';
    public $lowerLimit = 0;
    public $start="-14d";
    public $end="now";


    //todo: maybe group them into one array?
    public $defs = array();
    public $cdefs = array();
    public $vdefs = array();


    public $displayDataRules = array();

    public $graphWidth = 400;
    public $graphHeight = 100;

    public $onlyGraph = false;
    public $aliases ='';
    public $dbPath;
    public $imagePath;

    function __construct($dbLocation, $imageLocation)
    {
        $this->dbPath = $dbLocation;
        $this->imagePath = $imageLocation;
    }

    public function setTitle($text) {
        $this->title = $text;
        return $this;
    }

    public function setVerticalLabel($text) {
        $this->verticalLabel = $text;
        return $this;
    }

    public function setLowerLimit( $lowerLimit = 0)
    {
        $this->lowerLimit = $lowerLimit;
        return $this;
    }

    public function setStart($start = '-14d')
    {
        $this->start = $start;
        return $this;
    }

    public function setEnd($end = 'now')
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @param string $filename Absolute file location path. please.
     * @return PmxRrdGraph
     */
    public function setFileName($filename){
        $this->filename = $filename;
        return $this;
    }

    //graph display definition functions

    /**
     * @param $varName
     * @param $legend
     * @param int $hexColor
     * @param int $width
     * @param bool $stack
     * @return PmxRrdGraph
     */
    public function line($varName, $legend, $hexColor=380470, $width=2, $stack = false)
    {
        if($stack){ $stack = ':STACK'; } else { $stack = ''; }
        $this->displayDataRules[] = "LINE$width:$varName#$hexColor:$legend".$stack;
        return $this;
    }

    /**
     * @param $varName
     * @param $offsetTime
     * @return PmxRrdGraph
     */
    public function shift($varName, $offsetTime)
    {
        $this->displayDataRules[] = "SHIFT:$varName:$offsetTime";
        return $this;
    }


    /**
     * @param $varName
     * @param $legend
     * @param int $hexColor
     * @param bool $stack
     * @return PmxRrdGraph
     */
    public function area($varName, $legend, $hexColor=380470, $stack = false)
    {
        $this->displayDataRules[] = "AREA:$varName#$hexColor:$legend".$stack;
        return $this;
    }


    /**
     * TICK:имя-переменной#rrggbbaa[:доля-от-оси-Y[:легенда]] (вертикальные засечки на месте каждого определённого и ненулевого значения переменной)
     * @param $varName
     * @param int $hexColor
     * @param null $legend
     * @param null $yAxeTick
     * @return PmxRrdGraph
     */
    public function tick($varName, $hexColor=001122, $legend = null, $yAxeTick =null )
    {

        if(!empty($yAxeTick))
        {
            $suffix = ":$yAxeTick:$legend";
        }else {
            $suffix ='';
        }

        $this->displayDataRules[] = "TICK:$varName#$hexColor".$suffix;
        return $this;
    }

    //end graph display definition functions

    // data definition

    public function addDef($varName, $dsName, $consolidationFunction = 'AVERAGE', $fileName = null, $step = null, $start = null, $end = null, $reduceConsolidationFunction  = null)
    {
        if(empty($fileName)) {
            $fileName = $this->filename;
        }
        if(!file_exists($fileName)) {
            throw new RuntimeException('FUck Ya! try to use existed db');
        }



        if(!empty($start)) {
            $start = ":start=$start";
        }

        if(!empty($end)) {
            $end = ':end='.$end;
        }

        if(empty($end) && !empty($start)) { $end = ':end=now'; }

        if(!in_array($dsName, $this->getDataSourceFromDb($fileName) ) )
        {
            throw new RuntimeException('Fyuck Ya! try to use existed DS');
        }

        //todo: check for DS existance in selected filename;
        $this->defs[] = "DEF:$varName=$fileName:$dsName:$consolidationFunction".$end.$start;
        return $this;
    }

    public function addCDef($varName, $reversePolishNotation)
    {
        $this->cdefs[] = "CDEF:$varName=$reversePolishNotation";
        return $this;
    }

    public function addVDef($varName, $reversePolishNotation)
    {
        $this->vdefs[] = "VDEF:$varName=$reversePolishNotation";
        return $this;
    }

    // end data definition

    public function getOptions()
    {
        $opt = array(
            "--start", $this->start,

            "--title=".$this->title,
            "--vertical-label=".$this->verticalLabel,
            "--lower-limit=".$this->lowerLimit,
            "--width=".$this->graphWidth,
            "--height=".$this->graphHeight,
        );

        if($this->onlyGraph) {
            $opt[] = '--only-graph';
        }
        foreach($this->defs as $def) {
            $opt[] = $def;
        }
        foreach($this->cdefs as $def) {
            $opt[] = $def;
        }
        foreach($this->vdefs as $def) {
            $opt[] = $def;
        }
        foreach($this->displayDataRules as $lines) {
            $opt[] = $lines;
        }
        return $opt;
    }


    public function doDraw()
    {

        echo "<pre>";
        var_dump($this->getOptions());
        $outputFileName = "x.png";

        try{
            $ret = rrd_graph($outputFileName, $this->getOptions());

            if( !is_array($ret) )
            {
                $err = rrd_error();
                var_dump($err);

                echo "rrd_graph() ERROR: $err\n";
            }
            else {
//        echo "\nServer [$serverName] Status OK ";
            }

        } catch( Exception $e) {
            echo "hujeta";
        }


    }

    function rrdinfo($filename) {

        /*
           Inner functions to make them inaccesible from the outside of the main function
        */
        function add($key, $value, &$main_table) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    add($k, $v, $main_table[$key]);
                }
            } else {
                $main_table[$key] = $value;
            }
        }

        function toobj($key, $value) {
            $matches = array();
            if (preg_match('/^\\[(.*)\\]$/', $key, $matches)) {
                $key = $matches[1];
            }
            if (preg_match('/(.*?)\\[(.*?)\\]\\.(.*)/', $key, $matches)) {
                $matches2 = array();
                if (preg_match('/(.*?)\\[(.*?)\\]\\.(.*)/', $matches[3], $matches2)) {
                    $ret_key = $matches[1];
                    list($k, $v) = toobj($matches[3], $value);
                    $ret_val = array($matches[2] => array($k => $v));
                } else {
                    $ret_key = $matches[1];
                    $ret_val = array($matches[2] => array ($matches[3] => $value));
                }
            } else {
                $ret_key = $key;
                $ret_val = $value;
            }
            return array($ret_key, $ret_val);
        }

        /*
           Main program code
        */
        $main_table = array();
        $info = rrd_info($filename);
        foreach ($info as $ds_key => $ds_value) {
            list ($key, $value) = toobj($ds_key, $ds_value);
            add($key, $value, $main_table);
        }
        return $main_table;
    }
}



