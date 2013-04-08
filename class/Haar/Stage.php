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

    /**
     * Valid stage
     * 
     * @param Image $img
     * @param int $x
     * @param int $y
     * @param number $onde
     * @return boolean
     */
    public function valideStage(Image $img, $x, $y, $onde) {
        $total = 0;
        $gray = $img->getIntegral();
        $square = $img->getIntegralSquare();
        
        foreach ($this->features as $f)
            $total += $f->evalFeature($gray, $square, $x, $y, $onde);

        return $total > $this->threshold;
    }
}
