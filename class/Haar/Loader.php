<?php

include("Stage.php");

/**
 * Load a Haar cascade xml file generate by OpenCV
 *
 * @author Benjamin Georgeault & Jéméry Papin
 *
 */
class Loader {
    /**
     * List of Stages
     * @var array:Stage
     */
    public $stages;

    /**
     * Width of one stumb
     * 
     * @var integer
     */
    public $width;

    /**
     * Height of one stumb
     *
     * @var integer
     */
    public $height;

    /**
     * Load a Haar cascade xml file generate by OpenCV
     * 
     * @param string $file
     */
    public function __construct($file) {
        if (!is_file($file))
            die("This is not a file.");

        $xml = simplexml_load_file($file);
        if ($xml == false)
            die("Error load xml file.");

        $rootData = $xml->children()->children();

        // size
        list($this->width, $this->height) = explode(" ",
                strval($rootData->size));

        $this->stages = array();

        // stages loop
        foreach ($rootData->stages->children() as $s) {
            $stage = new Stage(floatval($s->stage_threshold));

            // features loop
            foreach ($s->trees->children() as $t) {
                $f = $t->_;

                $feature = new Feature($this, floatval($f->threshold),
                        floatval($f->left_val), floatval($f->right_val));

                // rect loop
                foreach ($f->feature->rects->_ as $r) {
                    $txt = explode(" ", strval($r));
                    $x1 = intval($txt[0]);
                    $x2 = intval($txt[1]);
                    $y1 = intval($txt[2]);
                    $y2 = intval($txt[3]);
                    $coef = floatval($txt[4]);

                    $feature->rects[] = new Rect($x1, $x2, $y1, $y2, $coef);
                }

                $stage->features[] = $feature;
            }

            $this->stages[] = $stage;
        }
    }
}
