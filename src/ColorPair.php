<?php

namespace Breadthe\PhpContrast;

class ColorPair
{
    public $ratio;
    public $color1;
    public $color2;

    public function __construct(float $ratio = null, string $color1 = null, string $color2 = null)
    {
        $this->ratio = $ratio;
        $this->color1 = $color1;
        $this->color2 = $color2;
    }

    /**
     * '000' => '#000000'
     * '000000' => '#000000'
     */
//    protected function normalize($color)
//    {
//        return $color; //
//    }
}
