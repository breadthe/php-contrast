<?php

namespace Breadthe\PhpContrast;

use Tightenco\Collect\Support\Collection;

class TailwindColor implements Color
{
    protected $colors;

    protected $minRatio;

    public function __construct()
    {
        $this->colors = $this->getDefaultTailwindColors();
    }

    /**
     * Returns all the default Tailwind colors
     */
    public static function colors(): array
    {
        return (new static)->colors->toArray();
    }

    public function getColors(): array
    {
        return $this->colors->toArray();
    }

    /**
     * Returns a random Tailwind color
     */
    public static function random(): HexColor
    {
        $tailwindColors = (new static)->colors;

        return $tailwindColors[rand(0, count($tailwindColors) - 1)];
    }

    public static function minContrast($ratio): self
    {
        if ($ratio < HexColorPair::MIN_RATIO) {
            $ratio = HexColorPair::MIN_RATIO;
        }

        if ($ratio > HexColorPair::MAX_RATIO) {
            $ratio = HexColorPair::MAX_RATIO;
        }

        $twColor = new static;
        $twColor->minRatio = $ratio;

        return $twColor;
    }

    public static function merge(array $customPalette): self
    {
        $twColor = new static;

        $customPalette = collect($customPalette)
            ->map(function ($hex, $name) {
                return HexColor::make($hex, $name);
            });

        $twColor->colors = $twColor->colors->merge($customPalette->flatten());

        return $twColor;
    }

    /**
     * Returns a random pair of accessible (min. contrast 3:1) Tailwind colors
     */
    public static function randomPair(): HexColorPair
    {
        return (new static)->getRandomPair();
    }

    public function getRandomPair(): HexColorPair
    {
        $minRatio = $this->minRatio ?? HexColorPair::MIN_RATIO;

        $fg = static::random();

        return $this->getRandomPairMinRatio($fg, $minRatio);
    }

    protected function getRandomPairMinRatio($fg, $minRatio): HexColorPair
    {
        do {
            $bg = static::random();
            $pair = HexColorPair::make($fg, $bg);
        } while ($pair->ratio <= $minRatio);

        return $pair;
    }

    protected function getDefaultTailwindColors(): Collection
    {
        $twColorsArr = $this->parseDefaultTailwindColors();

        return $this->flatten($twColorsArr['colors']);
    }

    protected function flatten(array $twColorsArr): Collection
    {
        $colors = new Collection();

        collect($twColorsArr)
            ->each(function ($color, $label) use ($colors) {
                if (is_array($color)) {
                    foreach ($color as $shade => $value) {
                        $colors->push(HexColor::make($value, "{$label}-{$shade}"));
                    }
                } else {
                    $colors->push(HexColor::make($color, $label));
                }
            });

        return $colors;
    }

    protected function parseDefaultTailwindColors()
    {
        return json_decode(file_get_contents(__DIR__ . '/../stubs/twcolors.json'), true);
    }
}
