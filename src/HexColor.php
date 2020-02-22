<?php

namespace Breadthe\PhpContrast;

class HexColor implements Color
{
    public $hex;
    public $name;

    public function __construct(string $hex, string $name = null)
    {
        $this->hex = $this->normalize($hex);
        $this->name = $name;
    }

    public static function make(string $hex, string $name = null): self
    {
        return new static($hex, $name);
    }

    public static function random(): self
    {
        return new static(self::randomColor());
    }

    /**
     * '#abc' or 'abc' => '#aabbcc'
     * '#abcdef' or 'abcdef' => '#abcdef'.
     */
    protected function normalize(string $hexColor)
    {
        // '#abcdef' or 'abcdef'
        if (preg_match('/^\s*#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})\s*$/i', $hexColor, $matches)) {
            return sprintf('#%s%s%s', $matches[1], $matches[2], $matches[3]);
        }

        // '#abc' or 'abc'
        if (preg_match('/^\s*#?([0-9a-f])([0-9a-f])([0-9a-f])\s*$/i', $hexColor, $matches)) {
            return sprintf('#%s%s%s', str_repeat($matches[1], 2), str_repeat($matches[2], 2),
                str_repeat($matches[3], 2));
        }

        // TODO throw
    }

    /**
     * @see https://stackoverflow.com/questions/5614530/generating-a-random-hex-color-code-with-php
     */
    protected static function randomColor()
    {
        return static::randomColorPart().static::randomColorPart().static::randomColorPart();
    }

    protected static function randomColorPart()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}
