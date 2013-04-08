<?php

include("Feature.php");

/**
 * Stage of Haar classification
 *
 * @author Benjamin Georgeault & Jéméry Papin
 *
 */
class Stage {
    /**
     * List of Features
     * @var array:Feature
     */
    public $features;
    
    /**
     * @var float
     */
    public $threshold;

    /**
     * @param float $threshold
     */
    public function __construct($threshold) {
        $this->threshold = $threshold;
        $this->features = array();
    }
}
