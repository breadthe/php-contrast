<?php

namespace Breadthe\PhpContrast\Tests;

use Breadthe\PhpContrast\HexColor;
use Breadthe\PhpContrast\HexColorPair;
use PHPUnit\Framework\TestCase;

class HexColorPairTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideContrastData
     */
    public function it_returns_the_correct_contrast_ratio(float $contrastRatio, array $colorPair)
    {
        $fg = HexColor::make($colorPair[0]);
        $bg = HexColor::make($colorPair[1]);
        $pair = HexColorPair::make($fg, $bg);

        // Test the property
        self::assertEquals($contrastRatio, $pair->ratio);

        // Test the static constructor
        self::assertEquals($contrastRatio, HexColorPair::ratio($fg, $bg));
    }

    /**
     * @test
     *
     * For more assurance, call the following assertions inside a loop
     */
    public function it_returns_random_color_pairs_of_minimum_3_to_1_ratio()
    {
        $minRatio = 3;

        $pair = HexColorPair::random();
        self::assertGreaterThanOrEqual($minRatio, $pair->ratio);

        // Color pairs with a desired min ratio < 3 will default to 3
        $desiredMinRatio = 2;
        $pair = HexColorPair::minContrast($desiredMinRatio)->getRandom();
        self::assertGreaterThanOrEqual($minRatio, $pair->ratio);

        // Color pairs are generated with the minimum specified ratio
        $desiredMinRatio = 4.5;
        $pair = HexColorPair::minContrast($desiredMinRatio)->getRandom();
        self::assertGreaterThanOrEqual($desiredMinRatio, $pair->ratio);
    }

    /** @test */
    public function a_sibling_of_minimum_contrast_is_generated_correctly()
    {
        $minRatio = 3;
        $hexColor = '#cccccc';

        // Sibling is generated with a minimum contrast ratio of 3.0
        $sibling = HexColorPair::sibling($hexColor);
        $pair = HexColorPair::make(HexColor::make($hexColor), $sibling);
        self::assertGreaterThanOrEqual($minRatio, $pair->ratio);

        // Sibling is generated with the minimum specified ratio
        $desiredMinRatio = 4.5;
        $sibling = HexColorPair::minContrast($desiredMinRatio)->getSibling($hexColor);
        $pair = HexColorPair::make(HexColor::make($hexColor), $sibling);
        self::assertGreaterThanOrEqual($desiredMinRatio, $pair->ratio);
    }

    public function provideContrastData()
    {
        yield 'maximum contrast' => [
            21,
            ['000', 'fff'],
        ];
        yield 'no contrast - black on black' => [
            1,
            ['000', '000'],
        ];
        yield 'no contrast - white on white' => [
            1,
            ['fff', 'fff'],
        ];
        yield 'high contrast' => [
            18.4,
            ['300', 'fff'],
        ];
        yield 'low contrast' => [
            1.1,
            ['300', '000'],
        ];
    }
}
