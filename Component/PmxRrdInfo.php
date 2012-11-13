<?php
/**
 * Created by JetBrains PhpStorm.
 * @author: pomaxa none <pomaxa@gmail.com>
 * @date: 10/24/12
 */

namespace Pmx\Bundle\RrdBundle\Component;

class PmxRrdInfo
{
    /**@var string $filename file path */
    public $filename;
    /** @var array $data with all information about database */
    public $data;

    /**
     * @param string $filename Rrd database filename
     */
    public function __construct($filename = null)
    {
        if(!empty($filename)) {
            $this->setFileName($filename);
            $this->transform();
        }
    }

    /**
     * @param string $filename
     * @return PmxRrdInfo
     */
    public function setFileName($filename)
    {
        //todo: validate if file is rrd database
        $this->filename = $filename;
        return $this;
    }

    public function alias_rrd_info()
    {
        return rrd_info($this->filename);
    }

    /**
     * transform info from rrd_info into nice array.
     *
     * @return PmxRrdInfo
     */
    function transform()
    {
        $this->data = array();
        $info = rrd_info($this->filename);
        foreach ($info as $ds_key => $ds_value) {
            list ($key, $value) = $this->toobj($ds_key, $ds_value);
            $this->add($key, $value);
        }

        return $this;
    }

    /**
     * Get info array about
     * @return mixed
     */
    public function getInfo()
    {
        if(empty($this->data))
        {
            $this->transform();
        }
        return $this->data;
    }

    /**
     * Get names of DataSources in RRD file
     *
     * @return array
     */
    public function getDSNames()
    {
        return array_keys($this->data['ds']);
    }

    protected function add($key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->add($k, $v);
            }
        } else {
            $this->data[$key] = $value;
        }
    }

    protected function toobj($key, $value)
    {
        $matches = array();
        if (preg_match('/^\\[(.*)\\]$/', $key, $matches)) {
            $key = $matches[1];
        }
        if (preg_match('/(.*?)\\[(.*?)\\]\\.(.*)/', $key, $matches)) {
            $matches2 = array();
            if (preg_match('/(.*?)\\[(.*?)\\]\\.(.*)/', $matches[3], $matches2)) {
                $ret_key = $matches[1];
                list($k, $v) = $this->toobj($matches[3], $value);
                $ret_val = array($matches[2] => array($k => $v));
            } else {
                $ret_key = $matches[1];
                $ret_val = array($matches[2] => array($matches[3] => $value));
            }
        } else {
            $ret_key = $key;
            $ret_val = $value;
        }

        return array($ret_key, $ret_val);
    }
}
