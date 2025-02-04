<?php
namespace Test\Feature\TRegx\CleanRegex\Replace\atMost;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Exception\ReplacementExpectationFailedException;

class ReplacePatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldIgnore_first_atMost_once()
    {
        // when
        $result = pattern('Foo')->replace('Foo Bar Bar Bar')->first()->atMost()->with('Bar');

        // then
        $this->assertSame('Bar Bar Bar Bar', $result);
    }

    /**
     * @test
     */
    public function shouldThrow_first_atMost_two()
    {
        // then
        $this->expectException(ReplacementExpectationFailedException::class);
        $this->expectExceptionMessage('Expected to perform at most 1 replacement(s), but at least 2 replacement(s) would have been performed');

        // when
        pattern('Foo')->replace('Foo Foo Bar Bar')->first()->atMost()->with('Bar');
    }

    /**
     * @test
     */
    public function shouldIgnore_first_atMost_none()
    {
        // when
        $result = pattern('Foo')->replace('Bar Bar Bar Bar')->first()->atMost()->with('Bar');

        // then
        $this->assertSame('Bar Bar Bar Bar', $result);
    }

    /**
     * @test
     */
    public function shouldIgnore_two_atMost_once()
    {
        // when
        $result = pattern('Foo')->replace('Foo Bar Bar Bar')->only(2)->atMost()->with('Bar');

        // then
        $this->assertSame('Bar Bar Bar Bar', $result);
    }

    /**
     * @test
     */
    public function shouldIgnore_two_atMost_twice()
    {
        // when
        $result = pattern('Foo')->replace('Foo Foo Bar Bar')->only(2)->atMost()->with('Bar');

        // then
        $this->assertSame('Bar Bar Bar Bar', $result);
    }

    /**
     * @test
     */
    public function shouldIgnore_two_atMost_thrice()
    {
        // then
        $this->expectException(ReplacementExpectationFailedException::class);
        $this->expectExceptionMessage('Expected to perform at most 2 replacement(s), but at least 3 replacement(s) would have been performed');

        // when
        pattern('Foo')->replace('Foo Foo Foo Bar')->only(2)->atMost()->with('Bar');
    }
}
