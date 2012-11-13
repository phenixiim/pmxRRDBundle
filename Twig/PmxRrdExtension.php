<?php

namespace Pmx\Bundle\RrdBundle\Twig;

class PmxRrdExtension extends \Twig_Extension
{

    protected $diContainer;

    public function __construct($diContainer)
    {
        $this->diContainer = $diContainer;
    }

    public function getFilters()
    {
        return array(
            'getRrdImage' => new \Twig_Function_Method($this, 'fetchGraphImageForRrdByName'),
            'getRrdImageUrl' => new \Twig_Function_Method($this, 'fetchGraphUrlForRrdByName'),
        );
    }

    public function getRrdImage($databaseName, $path = null)
    {
        $url = $this->getRrdImageUrl($databaseName, $path);
        return "<img src='$url' />";
    }

    public function getRrdImageUrl($databaseName, $path = null)
    {
        $pmxGraph = $this->diContainer->get('pmx_rrd.graph');
        //todo;
        return '';
    }


    public function getName()
    {
        return 'pmx_rrd_extension';
    }
}