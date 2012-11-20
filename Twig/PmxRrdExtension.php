<?php

namespace Pmx\Bundle\RrdBundle\Twig;

class PmxRrdExtension extends \Twig_Extension
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            'rrd_image' => new \Twig_Filter_Method($this, 'getRrdImage'),
            'rrd_image_url' => new \Twig_Filter_Method($this, 'getRrdImageUrl'),
        );
    }

    public function getRrdImage($databaseName, $path = null)
    {
        $url = $this->getRrdImageUrl($databaseName, $path);

        return sprintf('<img src="%s" />', $url);
    }

    public function getRrdImageUrl($databaseName, $path = null)
    {
        if (null === $path) {
            $path = $this->container->get('pmx_rrd.graph_location');
        }

        return sprintf('%s/%s.png', $path, $databaseName);
    }

    public function getName()
    {
        return 'pmx_rrd_extension';
    }
}