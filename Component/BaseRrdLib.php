<?php

namespace Pmx\Bundle\RrdBundle\Component;

class BaseRrdLib
{
    /**
     * @param $path
     *
     * @return bool
     */
    protected function mkpath($path)
    {
        if ($path === null) {
            return;
        }
        if (@mkdir($path) or file_exists($path)) {
            chmod($path, 0777);
            return true;
        }

        return ($this->mkpath(dirname($path)) and mkdir($path));
    }

}