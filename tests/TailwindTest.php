<?php

namespace Breadthe\PhpContrast\Tests;

use Breadthe\PhpContrast\HexColor;
use Breadthe\PhpContrast\HexColorPair;
use Breadthe\PhpContrast\TailwindColor;
use PHPUnit\Framework\TestCase;

class TailwindTest extends TestCase
{
    /** @test */
    public function it_generates_the_correct_object_types()
    {
        $twColors = TailwindColor::colors();
        self::assertTrue(is_array($twColors));

        $randomTwColor = TailwindColor::random();
        self::assertTrue(is_a($randomTwColor, HexColor::class));

        $randomPair = TailwindColor::randomPair();
        self::assertTrue(is_a($randomPair, HexColorPair::class));
        self::assertTrue(is_a($randomPair->fg, HexColor::class));
        self::assertTrue(is_a($randomPair->bg, HexColor::class));
    }

    /** @test */
    public function it_returns_random_color_pairs_of_minimum_3_to_1_ratio()
    {
        $minRatio = 3;

        $randomPair = TailwindColor::randomPair();
        self::assertGreaterThanOrEqual($minRatio, $randomPair->ratio);

        // Color pairs with a desired min ratio < 3 will default to 3
        $desiredMinRatio = 2;
        $randomPair = TailwindColor::minContrast($desiredMinRatio)->getRandomPair();
        self::assertGreaterThanOrEqual($minRatio, $randomPair->ratio);

        // Color pairs are generated with the minimum specified ratio
        $desiredMinRatio = 4.5;
        $randomPair = TailwindColor::minContrast($desiredMinRatio)->getRandomPair();
        self::assertGreaterThanOrEqual($minRatio, $randomPair->ratio);
    }

    /** @test */
    public function it_can_merge_default_and_custom_colors()
    {
        $customPalette = json_decode(file_get_contents(__DIR__.'/../stubs/custom-palette.json'), true);

        $colors = TailwindColor::merge($customPalette)->getColors();
        $customPalette = collect($customPalette)
            ->map(function ($hex, $name) {
                return HexColor::make($hex, $name);
            })
            ->flatten()
            ->toArray();

        $randomCustomPaletteElement = $customPalette[rand(0, count($customPalette) - 1)];

        self::assertContainsEquals($randomCustomPaletteElement, $colors, false);
    }
}
