<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Parsing;

use Stringable;

final readonly class Span implements Stringable
{
    public function __construct(public Location $start, public Location $end)
    {
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function char(int $line, int $column): static
    {
        $location = new Location($line, $column);
        return new static($location, $location);
    }

    public function __toString(): string
    {
        return $this->isSingleChar() ? (string)$this->start : sprintf('%s-%s', $this->start, $this->end);
    }

    private function isSingleChar(): bool
    {
        return $this->start->equals($this->end);
    }
}
