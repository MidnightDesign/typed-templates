<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Parsing;

use Stringable;

use function sprintf;

final readonly class Location implements Stringable
{
    /**
     * @param positive-int $row
     * @param positive-int $column
     */
    public function __construct(public int $row, public int $column)
    {
    }

    public function __toString(): string
    {
        return sprintf('%d:%d', $this->row, $this->column);
    }

    public function equals(self $other): bool
    {
        return $this->row === $other->row && $this->column === $other->column;
    }
}
