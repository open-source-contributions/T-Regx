<?php
namespace Test\Unit\TRegx\CleanRegex;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Exception\TemplateFormatException;
use TRegx\CleanRegex\FormatTemplate;
use TRegx\CleanRegex\Internal\Format\LiteralTokenValue;

class FormatTemplateTest extends TestCase
{
    /**
     * @test
     * @dataProvider methods
     * @param string $method
     * @param array $arguments
     */
    public function shouldThrowForOverflowingLiteral(string $method, array $arguments): void
    {
        // given
        $template = new FormatTemplate('^&&$', '', false, [new LiteralTokenValue(), new LiteralTokenValue(), new LiteralTokenValue()]);

        // then
        $this->expectException(TemplateFormatException::class);
        $this->expectExceptionMessage('There are only 2 & tokens in template, but 3 builder methods were used');

        // when
        $template->$method(...$arguments);
    }

    /**
     * @test
     * @dataProvider methods
     * @param string $method
     * @param array $arguments
     */
    public function shouldThrowForMissingLiteral(string $method, array $arguments): void
    {
        // given
        $template = new FormatTemplate('^&&$', '', false, [new LiteralTokenValue()]);

        // then
        $this->expectException(TemplateFormatException::class);
        $this->expectExceptionMessage('There are 2 & tokens in template, but only 1 builder methods were used');

        // when
        $template->$method(...$arguments);
    }

    public function methods(): array
    {
        return [
            ['build', []],
            ['bind', [[]]],
            ['inject', [[]]],
        ];
    }

    /**
     * @test
     */
    public function shouldBuildBeImmutable(): void
    {
        // given
        $template = new FormatTemplate('^&&$', 's', false, [new LiteralTokenValue()]);

        // when
        $first = $template->literal();
        $second = $template->literal();
        $third = $template->literal();

        // then
        $this->assertSame('/^&&$/s', $first->build()->delimited());
        $this->assertSame('/^&&$/s', $second->build()->delimited());
        $this->assertSame('/^&&$/s', $third->build()->delimited());
    }

    /**
     * @test
     */
    public function shouldChoseDelimiter(): void
    {
        // given
        $template = new FormatTemplate('^&/$', '', false, [new LiteralTokenValue()]);

        // when
        $pattern = $template->build();

        // then
        $this->assertSame('#^&/$#', $pattern->delimited());
    }
}
