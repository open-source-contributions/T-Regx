<?php
namespace Test\Interaction\TRegx\CleanRegex\Remove;

use TRegx\CleanRegex\Internal\InternalPattern;
use TRegx\CleanRegex\Remove\RemovePattern;
use PHPUnit\Framework\TestCase;

class RemovePatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldRemoveAll()
    {
        // given
        $pattern = new RemovePattern(InternalPattern::standard('\d+'), 'My ip 172.168.13.2 address', -1);

        // when
        $result = $pattern->remove();

        // then
        $this->assertSame('My ip ... address', $result);
    }

    /**
     * @test
     */
    public function shouldRemoveLimit()
    {
        // given
        $pattern = new RemovePattern(InternalPattern::standard('\d+'), 'My ip 172.168.13.2 address', 2);

        // when
        $result = $pattern->remove();

        // then
        $this->assertSame('My ip ..13.2 address', $result);
    }
}
