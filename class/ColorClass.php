<?php

include("Image.php");

/**
 * Color Classifier
 * 
 * @author Benjamin Georgeault & Jéméry Papin
 *
 */
class ColorClass
{
    /**
     * Array of references colors values
     * 
     * @var array
     */
    static $colors = array("RED" => array(255, 0, 0),
            "GREEN" => array(0, 255, 0), "BLUE" => array(0, 0, 255),
            "YELLOW" => array(255, 255, 0), "MANGENTA" => array(255, 0, 255),
            "CYAN" => array(0, 255, 255), "WHITE" => array(255, 255, 255),
            "BLACK" => array(0, 0, 0));

    /**
     * GD ressource for references colors palette
     * 
     * @var ressource
     */
    static $palette = null;

    /**
     * Association array between colors and palette's indexes
     * 
     * @var array
     */
    static $palette_colors_link = array();

    /**
     * Constructor
     */
    function __construct()
    {
        if (self::$palette == null)
            $this->genePalette();
    }

    /**
     * Count of pixel link to references colors
     * 
     * @param ressource $rsc GD ressource of image
     * @param number $h Height of image
     * @param number $w Width of image
     * @return array
     */
    private function classifPixel($rsc, $h, $w)
    {
        $result = array();

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $index = imagecolorat($rsc, $x, $y);
                $tab = imagecolorsforindex($rsc, $index);

                $proche = imagecolorclosest(self::$palette, $tab["red"],
                        $tab["green"], $tab["blue"]);

                if (!isset($result[$proche]))
                    $result[$proche] = 0;

                $result[$proche]++;
            }
        }

        arsort($result);

        return $result;
    }

    /**
     * Make percent array
     * 
     * @param array $classification Result of classifPixel()
     * @param number $h Height of image
     * @param number $w Width of image
     * @return array
     */
    private function organized($classification, $h, $w)
    {
        $total = $h * $w;
        $result = array();

        foreach ($classification as $index => $nb) {
            $result[self::$palette_colors_link[$index]] = ($nb / $total)
                    * 100;
        }

        return $result;
    }

    /**
     * Make reference palette colors
     */
    private function genePalette()
    {
        self::$palette = imagecreate(1, 1);

        foreach (self::$colors as $key => $color) {
            list($r, $v, $b) = $color;

            $index = imagecolorallocate(self::$palette, $r, $v, $b);
            self::$palette_colors_link[$index] = $key;
        }
    }

    /**
     * Get classification array
     * 
     * @param Image $image
     * @return array
     */
    public function getArrayDominance(Image $image, $percent = true)
    {
        $classif = $this
                ->classifPixel($image->getThumb(), $image->getHeightT(),
                        $image->getWidthT());

        if ($percent)
            return $this
                    ->organized($classif, $image->getHeightT(),
                            $image->getWidthT());

        return $classif;
    }

    /**
     * Get hexa code of references colors
     * 
     * @return array:string
     */
    public function getReferenceColor()
    {
        $result = array();

        foreach (self::$colors as $key => $color) {
            list($r, $v, $b) = $color;

            $result[$key] = sprintf("%02X%02X%02X", $r, $v, $b);
        }

        return $result;
    }
}
