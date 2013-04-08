<?php

/**
 * Rect of Haar classification
 *
 * @author Benjamin Georgeault & Jéméry Papin
 *
 */
class Rect {
    /**
     * @var integer
     */
    public $x1;
    
    /**
     * @var integer
     */
    public $x2;
    
    /**
     * @var integer
     */
    public $y1;
    
    /**
     * @var integer
     */
    public $y2;
    
    /**
     * @var float
     */
    public $coef;

    /**
     * 
     * @param integer $x1
     * @param integer $x2
     * @param integer $y1
     * @param integer $y2
     * @param float $coef
     */
    public function __construct($x1, $x2, $y1, $y2, $coef) {
        $this->x1 = $x1;
        $this->x2 = $x2;
        $this->y1 = $y1;
        $this->y2 = $y2;
        $this->coef = $coef;
    }
}
