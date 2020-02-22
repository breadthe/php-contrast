<?php

namespace Breadthe\PhpContrast;

class HexColorPair
{
    const MIN_RATIO = 3.0;
    const MAX_RATIO = 4.5; // hard cap on the max requested ratio, to mitigate performance issues

    public $ratio; // computed contrast ratio between fg : bg

    // Calling these foreground/background instead of a more generic "color 1"/"color 2"
    // Doesn't really matter, they can be used interchangeably
    public $fg; // foreground (text) color
    public $bg; // background color

    protected $minRatio;

    public function __construct(HexColor $fg = null, HexColor $bg = null)
    {
        $this->fg = $fg;
        $this->bg = $bg;
        $this->ratio = static::calculateRatio($fg, $bg);
    }

    public static function make(HexColor $fg, HexColor $bg)
    {
        return new static($fg, $bg);
    }

    public static function ratio(HexColor $fg, HexColor $bg)
    {
        return (new static($fg, $bg))->ratio;
    }

    public static function random(): self
    {
        return (new static)->getRandom();
    }

    public static function sibling(string $hexValue): HexColor
    {
        return (new static)->getSibling($hexValue);
    }

    public static function minContrast($ratio): self
    {
        if ($ratio < self::MIN_RATIO) {
            $ratio = self::MIN_RATIO;
        }

        if ($ratio > self::MAX_RATIO) {
            $ratio = self::MAX_RATIO;
        }

        $pair = new static;
        $pair->minRatio = $ratio;

        return $pair;
    }

    public function getRandom(): self
    {
        $minRatio = $this->minRatio ?? self::MIN_RATIO;

        $fg = HexColor::random();

        return $this->getRandomPairMinRatio($fg, $minRatio);
    }

    public function getSibling(string $hexValue): HexColor
    {
        $minRatio = $this->minRatio ?? self::MIN_RATIO;

        $pair = $this->getRandomPairMinRatio(HexColor::make($hexValue), $minRatio);

        return $pair->bg;
    }

    protected static function calculateRatio(HexColor $fg = null, HexColor $bg = null)
    {
        if (! $fg || ! $bg) {
            return;
        }

        $fgLuminance = static::luminance($fg);
        $bgLuminance = static::luminance($bg);

        return round((max($fgLuminance, $bgLuminance) + 0.05) / (min($fgLuminance, $bgLuminance) + 0.05) * 10) / 10;
    }

    protected static function luminance(HexColor $color)
    {
        [$rHex, $gHex, $bHex] = static::rgbHexChannels($color);

        // Get decimal values
        $r8bit = base_convert($rHex, 16, 10);
        $g8bit = base_convert($gHex, 16, 10);
        $b8bit = base_convert($bHex, 16, 10);

        // Get sRGB values
        $rSrgb = $r8bit / 255;
        $gSrgb = $g8bit / 255;
        $bSrgb = $b8bit / 255;

        // Calculate luminance
        $r = ($rSrgb <= 0.03928) ? $rSrgb / 12.92 : pow((($rSrgb + 0.055) / 1.055), 2.4);
        $g = ($gSrgb <= 0.03928) ? $gSrgb / 12.92 : pow((($gSrgb + 0.055) / 1.055), 2.4);
        $b = ($bSrgb <= 0.03928) ? $bSrgb / 12.92 : pow((($bSrgb + 0.055) / 1.055), 2.4);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * '#abcdef' => ['ab', 'cd', 'ef'].
     */
    protected static function rgbHexChannels(HexColor $hexColor): array
    {
        $hex = substr($hexColor->hex, 1); // strip the #

        return [
            substr($hex, 0, 2),
            substr($hex, 2, 2),
            substr($hex, 4, 2),
        ];
    }

    protected function getRandomPairMinRatio($fg, $minRatio): self
    {
        do {
            $bg = HexColor::random();
            $pair = new static($fg, $bg);
        } while ($pair->ratio <= $minRatio);

        return $pair;
    }
}
