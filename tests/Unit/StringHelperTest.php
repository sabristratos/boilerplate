<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function test_string_can_be_reversed(): void
    {
        // Arrange
        $string = 'Hello World';

        // Act
        $result = strrev($string);

        // Assert
        $this->assertEquals('dlroW olleH', $result);
    }

    public function test_string_can_be_capitalized(): void
    {
        // Arrange
        $string = 'hello world';

        // Act
        $result = ucfirst($string);

        // Assert
        $this->assertEquals('Hello world', $result);
    }
}
