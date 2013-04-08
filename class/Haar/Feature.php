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

    /**
     * Eval of the feature
     * 
     * @param ressource $gray
     * @param ressource $square
     * @param int $x
     * @param int $y
     * @param number $onde
     * @return number
     */
    public function evalFeature($gray, $square, $x, $y, $onde) {
        $w = (int) ($onde * $this->loader->width);
        $h = (int) ($onde * $this->loader->height);
        $inv_area = 1 / ($w * $h);

        $xw = $x + $w;
        $yh = $y + $h;

        $total_x = $gray[$xw][$yh] + $gray[$x][$y] - $gray[$x][$yh]
                - $gray[$xw][$y];
        $total_x2 = $square[$xw][$yh] + $square[$x][$y] - $square[$x][$yh]
                - $square[$xw][$y];

        $moy = $total_x * $inv_area;
        $vnorm = $total_x2 * $inv_area - $moy * $moy;
        $vnorm = ($vnorm > 1) ? sqrt($vnorm) : 1;

        $total = 0;
        foreach($this->rects as $r) {
            $rx1 = $x + (int) ($onde * $r->x1);
            $rx2 = $x + (int) ($onde * ($r->x1 + $r->y1));
            $ry1 = $y + (int) ($onde * $r->x2);
            $ry2 = $y + (int) ($onde * ($r->x2 + $r->y2));

            $total += (int) (($gray[$rx2][$ry2]
                    - $gray[$rx1][$ry2] - $gray[$rx2][$ry1]
                    + $gray[$rx1][$ry1]) * $r->coef);
        }

        $total = $total * $inv_area;

        return ($total < $this->threshold * $vnorm ? $this->left_val
                : $this->right_val);
    }
}
