<?php
namespace Test\Unit\TRegx\CleanRegex\Match\MatchPattern\findFirst;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Test\Utils\Functions;
use TRegx\CleanRegex\Exception\SubjectNotMatchedException;
use TRegx\CleanRegex\Internal\InternalPattern;
use TRegx\CleanRegex\Match\Details\Detail;
use TRegx\CleanRegex\Match\Details\NotMatched;
use TRegx\CleanRegex\Match\MatchPattern;

class MatchPatternTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetMatch_withDetails()
    {
        // given
        $pattern = $this->getMatchPattern("Nice matching pattern");

        // when
        $pattern
            ->findFirst(function (Detail $detail) {
                // then
                $this->assertSame(0, $detail->index());
                $this->assertSame("Nice matching pattern", $detail->subject());
                $this->assertSame(['Nice', 'matching', 'pattern'], $detail->all());
                $this->assertSame(['N'], $detail->groups()->texts());
            })
            ->orThrow();
    }

    /**
     * @test
     */
    public function shouldGetMatch_withoutCollapsingOrMethod()
    {
        // given
        $pattern = $this->getMatchPattern("Nice matching pattern");

        // when
        $pattern
            ->findFirst(function (Detail $detail) {
                // then
                $this->assertSame("Nice matching pattern", $detail->subject());
            });
        // ->orThrow();
    }

    /**
     * @test
     */
    public function shouldGetFirst()
    {
        // given
        $pattern = $this->getMatchPattern("Nice matching pattern");

        // when
        $first1 = $pattern->findFirst('strToUpper')->orReturn(null);
        $first2 = $pattern->findFirst('strToUpper')->orThrow();
        $first3 = $pattern->findFirst('strToUpper')->orElse(Functions::fail());

        // then
        $this->assertSame('NICE', $first1);
        $this->assertSame('NICE', $first2);
        $this->assertSame('NICE', $first3);
    }

    /**
     * @test
     */
    public function shouldNotInvokeFirst_onNotMatchingSubject()
    {
        // given
        $pattern = $this->getMatchPattern('NOT MATCHING');

        // when
        $pattern->findFirst(Functions::fail())->orReturn(null);
        $pattern->findFirst(Functions::fail())->orElse(Functions::pass());
        try {
            $pattern->findFirst(Functions::fail())->orThrow();
        } catch (SubjectNotMatchedException $ignored) {
        }
    }

    /**
     * @test
     */
    public function should_onNotMatchingSubject_throw()
    {
        // given
        $pattern = $this->getMatchPattern('NOT MATCHING');

        // then
        $this->expectException(SubjectNotMatchedException::class);
        $this->expectExceptionMessage('Expected to get the first match, but subject was not matched');

        // when
        $pattern->findFirst('strRev')->orThrow();
    }

    /**
     * @test
     */
    public function should_onNotMatchingSubject_throw_userGivenException()
    {
        // given
        $pattern = $this->getMatchPattern('NOT MATCHING');

        // then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected to get the first match, but subject was not matched');

        // when
        $pattern->findFirst('strRev')->orThrow(InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function should_onNotMatchingSubject_throw_withMessage()
    {
        // given
        $pattern = $this->getMatchPattern('NOT MATCHING');

        // then
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected to get the first match, but subject was not matched');

        // when
        $pattern->findFirst('strRev')->orThrow(InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function should_onNotMatchingSubject_getDefault()
    {
        // given
        $pattern = $this->getMatchPattern('NOT MATCHING');

        // when
        $value = $pattern->findFirst('strRev')->orReturn('def');

        // then
        $this->assertSame('def', $value);
    }

    /**
     * @test
     */
    public function should_onNotMatchingSubject_call()
    {
        // given
        $pattern = $this->getMatchPattern('NOT MATCHING');

        // when
        $value = $pattern->findFirst('strRev')->orElse(Functions::constant('new value'));

        // then
        $this->assertSame('new value', $value);
    }

    /**
     * @test
     */
    public function should_onNotMatchingSubject_call_withDetails()
    {
        // given
        $pattern = new MatchPattern(InternalPattern::standard("(?:[A-Z])?[a-z']+ (?<group>.)"), 'NOT MATCHING');

        // when
        $pattern->findFirst('strRev')->orElse(function (NotMatched $details) {
            // then
            $this->assertSame('NOT MATCHING', $details->subject());
            $this->assertSame(['group'], $details->groupNames());
            $this->assertTrue($details->hasGroup('group'));
            $this->assertTrue($details->hasGroup(0));
            $this->assertTrue($details->hasGroup(1));
            $this->assertFalse($details->hasGroup('other'));
            $this->assertFalse($details->hasGroup(2));
        });
    }

    private function getMatchPattern($subject): MatchPattern
    {
        return new MatchPattern(InternalPattern::standard("([A-Z])?[a-z']+"), $subject);
    }
}
