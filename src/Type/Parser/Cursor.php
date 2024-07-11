<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

use Generator;

/**
 * @template T
 */
final class Cursor
{
    /** @var Generator<T> */
    private Generator $generator;

    /**
     * @param iterable<mixed, T> $items
     */
    public function __construct(iterable $items)
    {
        $this->generator = self::toGenerator($items);
    }

    /**
     * @param iterable<mixed, T> $items
     * @return Generator<T>
     */
    private static function toGenerator(iterable $items): Generator
    {
        foreach ($items as $item) {
            yield $item;
        }
    }

    /**
     * @return T | null
     */
    public function current(): mixed
    {
        return $this->generator->current();
    }

    /**
     * @return T
     */
    public function next(): mixed
    {
        $this->generator->next();
        return $this->generator->current();
    }
}
