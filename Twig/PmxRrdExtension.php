<?php

namespace Pmx\Bundle\RrdBundle\Twig;

use Pmx\Bundle\RrdBundle\Component\PmxRrdGraph;

class PmxRrdExtension extends \Twig_Extension
{
    protected $graph;

    public function __construct(PmxRrdGraph $graph)
    {
        $this->graph = $graph;
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

        return sprintf('<img src="%s" alt="rrd graph" />', $url);
    }

    public function getRrdImageUrl($databaseName, $path = null)
    {
        $this->graph->setImageName($databaseName);

        if (null !== $path) {
            $this->graph->setImagePath($path);
        }

        return $this->graph->getImageUrl();
    }

    public function getName()
    {
        return 'pmx_rrd_extension';
    }
}