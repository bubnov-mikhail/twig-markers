<?php

/**
 * @author Mihail Bubnov <bubnov.mihail@gmail.com>
 */

namespace bubnovKelnik\TwigMarkers;

abstract class TwigMarkers extends \Twig_Extension
{
    /**
     * Text's context
     * @var Entity 
     */
    protected $context;

    
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('markers', array($this, 'onMarkersFilter')),
        );
    }

    /**
     * Replace markers in string
     *
     * @param  $string String with markers in format %marker_without_spaces%
     * @param  $context Context of the string
     * @return String
     */
    public function onMarkersFilter($string='', $context=null)
    {
        if(!preg_match_all('/%(\S+?)%/', $string, $markers) || $context === null)
        {
            return $string;
        }
        $this->context = $context;
        $search = array();
        $replace = array();
        $markers[1] = array_unique($markers[1]);
        $markers[0] = array_unique($markers[0]);
        foreach($markers[1] as $m => $marker )
        {
            $methodFilter = 'onMarker'.ucfirst($marker);
            if(!method_exists($this, $methodFilter) || !$replaceTo = $this->$methodFilter())
            {
                continue;
            }
            $search[] = $markers[0][$m];
            $replace[] = $replaceTo;
        }
        return str_replace($search, $replace, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_markers_filter';
    }
    
    /**
     * Find requested context from provided
     * 
     * @param  $findContext Require Context (fully qualified class name)
     * @return Entity | false
     */
    abstract protected function findContext($findContext);
    
    
}
