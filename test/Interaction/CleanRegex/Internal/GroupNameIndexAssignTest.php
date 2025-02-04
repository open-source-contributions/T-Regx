<?php
namespace Test\Interaction\TRegx\CleanRegex\Internal;

use PHPUnit\Framework\TestCase;
use TRegx\CleanRegex\Exception\InternalCleanRegexException;
use TRegx\CleanRegex\Internal\GroupNameIndexAssign;
use TRegx\CleanRegex\Internal\Match\MatchAll\EagerMatchAllFactory;
use TRegx\CleanRegex\Internal\Match\MatchAll\MatchAllFactory;
use TRegx\CleanRegex\Internal\Model\Match\RawMatchOffset;
use TRegx\CleanRegex\Internal\Model\Matches\RawMatchesOffset;

class GroupNameIndexAssignTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetNameAndIndex_fromName()
    {
        // given
        $assign = $this->create();

        // when
        [$name, $index] = $assign->getNameAndIndex('third');

        // then
        $this->assertSame('third', $name);
        $this->assertSame(3, $index);
    }

    /**
     * @test
     */
    public function shouldGetNameAndIndex_fromIndex()
    {
        // given
        $assign = $this->create();

        // when
        [$name, $index] = $assign->getNameAndIndex(5);

        // then
        $this->assertSame('fifth', $name);
        $this->assertSame(5, $index);
    }

    /**
     * @test
     */
    public function shouldGetNullAndIndex_fromIndex_unnamedGroup()
    {
        // given
        $assign = $this->create();

        // when
        [$name, $index] = $assign->getNameAndIndex(2);

        // then
        $this->assertSame(null, $name);
        $this->assertSame(2, $index);
    }

    /**
     * @test
     */
    public function shouldGetNullAndIndex_fromIndex_wholeMatch()
    {
        // given
        $assign = $this->create();

        // when
        [$name, $index] = $assign->getNameAndIndex(0);

        // then
        $this->assertSame(null, $name);
        $this->assertSame(0, $index);
    }

    /**
     * @test
     */
    public function shouldThrow_onInvalidArgument()
    {
        // given
        $assign = $this->create();

        // then
        $this->expectException(InternalCleanRegexException::class);

        // when
        $assign->getNameAndIndex(true);
    }

    /**
     * @return GroupNameIndexAssign
     */
    private function create(): GroupNameIndexAssign
    {
        $match = [
            0       => '',
            'first' => '',
            1       => '',
            2       => '',
            'third' => '',
            3       => '',
            4       => '',
            'fifth' => '',
            5       => '',
        ];
        /** @var MatchAllFactory $factory */
        $factory = $this->createMock(MatchAllFactory::class);
        return new GroupNameIndexAssign(new RawMatchOffset($match, 0), $factory);
    }

    /**
     * @test
     */
    public function shouldCallFactory_byName_uneven()
    {
        // given
        $assign = $this->createWithMatchAllFactory_uneven();

        // when
        [$name, $index] = $assign->getNameAndIndex('name2');

        // then
        $this->assertSame('name2', $name);
        $this->assertSame(3, $index);
    }

    /**
     * @test
     */
    public function shouldCallFactory_byIndex_uneven()
    {
        // given
        $assign = $this->createWithMatchAllFactory_uneven();

        // when
        [$name, $index] = $assign->getNameAndIndex(3);

        // then
        $this->assertSame('name2', $name);
        $this->assertSame(3, $index);
    }

    private function createWithMatchAllFactory_uneven(): GroupNameIndexAssign
    {
        $match = [null, null];
        $matches = [
            0       => null,
            1       => null,
            'name'  => null,
            2       => null,
            'name2' => null,
            3       => null,
        ];

        return new GroupNameIndexAssign(new RawMatchOffset($match, 0), new EagerMatchAllFactory(new RawMatchesOffset($matches)));
    }

    /**
     * @test
     */
    public function shouldCallFactory_byName_even()
    {
        // given
        $assign = $this->createWithMatchAllFactory_even();

        // when
        [$name, $index] = $assign->getNameAndIndex('name2');

        // then
        $this->assertSame('name2', $name);
        $this->assertSame(3, $index);
    }

    /**
     * @test
     */
    public function shouldCallFactory_byName_emptyString()
    {
        // given
        $assign = $this->createWithMatchAllFactory_even();

        // then
        $this->expectException(InternalCleanRegexException::class);

        // when
        $assign->getNameAndIndex('');
    }

    /**
     * @test
     */
    public function shouldCallFactory_byIndex_even()
    {
        // given
        $assign = $this->createWithMatchAllFactory_even();

        // when
        [$name, $index] = $assign->getNameAndIndex(3);

        // then
        $this->assertSame('name2', $name);
        $this->assertSame(3, $index);
    }

    private function createWithMatchAllFactory_even(): GroupNameIndexAssign
    {
        $matches = [
            0       => null,
            1       => null,
            'name'  => null,
            2       => null,
            'name2' => null,
            3       => null,
        ];

        return new GroupNameIndexAssign(new RawMatchOffset($matches, 0), new EagerMatchAllFactory(new RawMatchesOffset($matches)));
    }

    /**
     * @test
     */
    public function shouldCallFactory_byName_missing()
    {
        // given
        $assign = $this->createWithMatchAllFactory_uneven();

        // then
        $this->expectException(InternalCleanRegexException::class);

        // when
        $assign->getNameAndIndex('missing');
    }

    /**
     * @test
     */
    public function shouldCallFactory_byIndex_missing()
    {
        // given
        $assign = $this->createWithMatchAllFactory_uneven();

        // then
        $this->expectException(InternalCleanRegexException::class);

        // when
        $assign->getNameAndIndex(14);
    }
}
