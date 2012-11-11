<?php

namespace Pmx\Bundle\RrdBundle\Twig;

class PmxRrdExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'pmx_rrd_graph' => new \Twig_Filter_Method($this, 'fetchGraphForRrd'),
        );
    }

    public function fetchGraphForRrd($databaseName, $path= null, $options = array())
    {
        $url = 'dummy.url';
        return $url;
    }

    public function getName()
    {
        return 'pmx_rrd_extension';
    }
}