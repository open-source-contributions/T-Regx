<?php
namespace TRegx\CleanRegex\Replace\Callback;

use TRegx\CleanRegex\Internal\InternalPattern as Pattern;
use TRegx\CleanRegex\Internal\Model\Matches\RawMatchesOffset;
use TRegx\CleanRegex\Internal\Replace\NonReplaced\SubjectRs;
use TRegx\CleanRegex\Internal\Subjectable;
use TRegx\SafeRegex\preg;

class ReplacePatternCallbackInvoker
{
    /** @var Pattern */
    private $pattern;
    /** @var Subjectable */
    private $subject;
    /** @var int */
    private $limit;
    /** @var SubjectRs */
    private $substitute;

    public function __construct(Pattern $pattern, Subjectable $subject, int $limit, SubjectRs $substitute)
    {
        $this->pattern = $pattern;
        $this->subject = $subject;
        $this->limit = $limit;
        $this->substitute = $substitute;
    }

    public function invoke(callable $callback, ReplaceCallbackArgumentStrategy $strategy): string
    {
        $result = $this->pregReplaceCallback($callback, $replaced, $strategy);
        if ($replaced === 0) {
            return $this->substitute->substitute($this->subject->getSubject()) ?? $result;
        }
        return $result;
    }

    private function pregReplaceCallback(callable $callback, ?int &$replaced, ReplaceCallbackArgumentStrategy $strategy): string
    {
        return preg::replace_callback($this->pattern->pattern,
            $this->getObjectCallback($callback, $strategy),
            $this->subject->getSubject(),
            $this->limit,
            $replaced);
    }

    private function getObjectCallback(callable $callback, ReplaceCallbackArgumentStrategy $strategy): callable
    {
        if ($this->limit === 0) {
            return static function () {
            };
        }
        return $this->createObjectCallback($callback, $strategy);
    }

    private function createObjectCallback(callable $callback, ReplaceCallbackArgumentStrategy $strategy): callable
    {
        $object = new ReplaceCallbackObject($callback, $this->subject, $this->analyzePattern(), $this->limit, $strategy);
        return $object->getCallback();
    }

    private function analyzePattern(): RawMatchesOffset
    {
        preg::match_all($this->pattern->pattern, $this->subject->getSubject(), $matches, \PREG_OFFSET_CAPTURE);
        return new RawMatchesOffset($matches);
    }
}
