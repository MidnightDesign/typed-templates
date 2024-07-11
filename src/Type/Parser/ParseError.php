<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

use RuntimeException;

use function sprintf;

abstract class ParseError extends RuntimeException
{
    final private function __construct(string $message, public readonly int $row, public readonly int $column)
    {
        parent::__construct(sprintf('%s (%d:%d)', $message, $row, $column));
    }

    /**
     * @param positive-int $row
     * @param positive-int $column
     */
    public static function create(string $message, int $row, int $column): static
    {
        return new static($message, $row, $column);
    }
}
