<?php

include("Rect.php");

/**
 * Feature of Haar classification
 *
 * @author Benjamin Georgeault & Jéméry Papin
 *
 */
class Feature {
    /**
     * List of Rect
     * @var array:Rect
     */
    public $rects;

    /**
     * @var float
     */
    public $threshold;

    /**
     * @var float
     */
    public $left_val;

    /**
     * @var float
     */
    public $right_val;

    /**
     * 
     * @var Loader
     */
    private $loader;

    /**
     * @param Loader $loader
     * @paramfloat $threshold
     * @param float $left_val
     * @param float $right_val
     */
    public function __construct(Loader $loader, $threshold, $left_val,
            $right_val) {
        $this->rects = array();
        $this->threshold = $threshold;
        $this->left_val = $left_val;
        $this->right_val = $right_val;

        $this->loader = $loader;
    }
}
