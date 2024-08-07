<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use function array_filter;

final readonly class Template
{
    /**
     * @param list<string | Placeholder> $parts
     */
    public function __construct(public array $parts)
    {
    }

    /**
     * @return list<Placeholder>
     */
    public function placeholders(): array
    {
        return array_filter($this->parts, static fn($part) => $part instanceof Placeholder);
    }
}
