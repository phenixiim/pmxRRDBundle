<?php
/**
 * Created by JetBrains PhpStorm.
 *
 * @author: pomaxa none <pomaxa@gmail.com>
 * @date: 10/19/12
 */
namespace Pmx\Bundle\RrdBundle\Component;

use RuntimeException;

class PmxRrdGraph
{
    public $filename;
    public $title = 'pmx graph example';
    public $verticalLabel = 'This is MyData';
    public $lowerLimit = 0;
    public $start = "-14d";
    public $end = "now";
    public $defs = array();
    public $cdefs = array();

    protected $imgFileName;

    protected function setImgFileName($filename)
    {
        $filename = $this->getImgFileNameFromFileName($filename);

        if($this->imagePath === null) {
            throw new \InvalidArgumentException('imagePath should not be null');
        }

        $this->mkpath($this->imagePath);

        $outputFileName = $this->imagePath.$filename.'.png';

        $this->imgFileName = $outputFileName;

        return $this;
    }

    public $vdefs = array();
    public $displayDataRules = array();
    public $graphWidth = 400;
    public $graphHeight = 100;
    public $onlyGraph = false;
    public $aliases = '';
    public $imagePath;

    /**
     * @param $dbLocation
     * @param $imageLocation
     */
    function __construct($dbLocation = null, $imageLocation = null)
    {
        //todo: add check if set/default/and passed
        $this->dbPath = $dbLocation;
        $this->imagePath = $imageLocation;
    }

    public function getImgFilePath(): string
    {
        return $this->imgFileName;
    }

    /**
     * @param $path
     *
     * @return bool
     */
    protected function mkpath($path)
    {
        if (@mkdir($path) or file_exists($path)) {
            return true;
        }

        return ($this->mkpath(dirname($path)) and mkdir($path));
    }

    public function setTitle($text)
    {
        $this->title = $text;

        return $this;
    }

    public function getImagePath()
    {
        return $this->imagePath;
    }

    public function setImagePath($imagePath)
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function setVerticalLabel($text)
    {
        $this->verticalLabel = $text;

        return $this;
    }

    public function setLowerLimit($lowerLimit = 0)
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
     *
     * @return PmxRrdGraph
     */
    public function setFileName($filename)
    {
        $this->setImgFileName($filename);

        $this->filename = $filename;

        return $this;
    }

    /**
     * @param      $varName
     * @param      $legend
     * @param int  $hexColor
     * @param int  $width
     * @param bool $stack
     *
     * @return PmxRrdGraph
     */
    public function line($varName, $legend, $hexColor = 380470, $width = 2, $stack = false)
    {
        if ($stack) {
            $stack = ':STACK';
        } else {
            $stack = '';
        }
        $this->displayDataRules[] = "LINE$width:$varName#$hexColor:$legend".$stack;

        return $this;
    }

    //graph display definition functions

    /**
     * @param $varName
     * @param $offsetTime
     *
     * @return PmxRrdGraph
     */
    public function shift($varName, $offsetTime)
    {
        $this->displayDataRules[] = "SHIFT:$varName:$offsetTime";

        return $this;
    }

    /**
     * @param      $varName
     * @param      $legend
     * @param int  $hexColor
     * @param bool $stack
     *
     * @return PmxRrdGraph
     */
    public function area($varName, $legend, $hexColor = 380470, $stack = false)
    {
        if ($stack) {
            $stack = ':STACK';
        } else {
            $stack = '';
        }
        $this->displayDataRules[] = "AREA:$varName#$hexColor:$legend".$stack;

        return $this;
    }

    /**
     * TICK:имя-переменной#rrggbbaa[:доля-от-оси-Y[:легенда]] (вертикальные засечки на месте каждого определённого и ненулевого значения переменной)
     *
     * @param      $varName
     * @param int  $hexColor
     * @param null $legend
     * @param null $yAxeTick
     *
     * @return PmxRrdGraph
     */
    public function tick($varName, $hexColor = 001122, $legend = null, $yAxeTick = null)
    {

        if (!empty($yAxeTick)) {
            $suffix = ":$yAxeTick:$legend";
        } else {
            $suffix = '';
        }

        $this->displayDataRules[] = "TICK:$varName#$hexColor".$suffix;

        return $this;
    }

    /**
     * @param string $varName
     * @param string $dsName
     * @param string $consolidationFunction
     * @param null   $fileName
     * @param null   $step
     * @param null   $start
     * @param null   $end
     * @param null   $reduceConsolidationFunction
     *
     * @return PmxRrdGraph
     * @throws \RuntimeException
     */
    public function addDef(
        $varName,
        $dsName,
        $consolidationFunction = 'AVERAGE',
        $fileName = null,
        $step = null,
        $start = null,
        $end = null,
        $reduceConsolidationFunction = null)
    {
        if (empty($fileName)) {
            $fileName = $this->filename;
        }
        if (!file_exists($fileName)) {
            throw new RuntimeException('FUck Ya! try to use existed db');
        }

        if (!empty($start)) {
            $start = ":start=$start";
        }

        if (!empty($end)) {
            $end = ':end='.$end;
        }

        if (empty($end) && !empty($start)) {
            $end = ':end=now';
        }

        //check if this DS exist in selected database file.
        if (!in_array($dsName, $this->getDataSourceFromDb($fileName))) {
            throw new RuntimeException('Fyuck Ya! try to use existed DS');
        }

        $this->defs[] = "DEF:$varName=$fileName:$dsName:$consolidationFunction".$end.$start;

        return $this;
    }

    //end graph display definition functions

    // data definition

    /**
     * @param string $varName
     * @param string $reversePolishNotation
     *
     * @return PmxRrdGraph
     */
    public function addCDef($varName, $reversePolishNotation)
    {
        //todo: validate $varName
        $this->cdefs[] = "CDEF:$varName=$reversePolishNotation";

        return $this;
    }

    /**
     * @param string $varName
     * @param string $reversePolishNotation
     *
     * @return PmxRrdGraph
     */
    public function addVDef($varName, $reversePolishNotation)
    {
        //todo: validate $varName
        $this->vdefs[] = "VDEF:$varName=$reversePolishNotation";

        return $this;
    }

    public function getOptions()
    {
        $opt = array(
            "--start",
            $this->start,
            "--end=".$this->end,
            "--title=".$this->title,
            "--vertical-label=".$this->verticalLabel,
            "--lower-limit=".$this->lowerLimit,
            "--width=".$this->graphWidth,
            "--height=".$this->graphHeight,
        );

        if ($this->onlyGraph) {
            $opt[] = '--only-graph';
        }
        foreach ($this->defs as $def) {
            $opt[] = $def;
        }
        foreach ($this->cdefs as $def) {
            $opt[] = $def;
        }
        foreach ($this->vdefs as $def) {
            $opt[] = $def;
        }
        foreach ($this->displayDataRules as $lines) {
            $opt[] = $lines;
        }

        return $opt;
    }

    // end data definition

    public function doDraw()
    {

        $outputFileName = $this->getImgFilePath();

        $ret = rrd_graph($outputFileName, $this->getOptions());
        if (!is_array($ret)) {
            $err = rrd_error();
            throw new RuntimeException("rrd_graph() ERROR: $err\n");
        }
    }

    protected function getDataSourceFromDb($filename)
    {
        $rrdInfo = new PmxRrdInfo($filename);

        return $rrdInfo->getDSNames();
    }

    protected function getImgFileNameFromFileName(string $filename): string
    {
        $x = pathinfo($filename);
        $output = $x['filename'].'_'.rand(1,9999999999);

        return $output;
    }
}



