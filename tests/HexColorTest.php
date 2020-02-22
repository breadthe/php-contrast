<?php

namespace Breadthe\PhpContrast\Tests;

use Breadthe\PhpContrast\HexColor;
use PHPUnit\Framework\TestCase;

class HexColorTest extends TestCase
{
    /** @test */
    public function a_hex_color_is_created_correctly()
    {
        $hexColor = HexColor::make('abc');
        self::assertNull($hexColor->name);
        self::assertEquals('#aabbcc', $hexColor->hex);

        $hexColor = HexColor::make('#abc');
        self::assertNull($hexColor->name);
        self::assertEquals('#aabbcc', $hexColor->hex);

        $hexColor = HexColor::make('abcdef');
        self::assertNull($hexColor->name);
        self::assertEquals('#abcdef', $hexColor->hex);

        $hexColor = HexColor::make('#abcdef');
        self::assertNull($hexColor->name);
        self::assertEquals('#abcdef', $hexColor->hex);

        $name = 'custom-color-name';
        $hexColor = HexColor::make('#abcdef', $name);
        self::assertEquals($name, $hexColor->name);
        self::assertEquals('#abcdef', $hexColor->hex);
    }

    /** @test */
    public function a_random_hex_color_is_generated_correctly()
    {
        $randomHexColor = HexColor::random();

        self::assertRegExp('/^#[0-9a-f]{6}$/i', $randomHexColor->hex);
        self::assertNull($randomHexColor->name);
    }
}
