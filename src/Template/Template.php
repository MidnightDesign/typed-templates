<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

final readonly class Template
{
    /**
     * @param list<string | Placeholder> $parts
     */
    public function __construct(public array $parts)
    {
    }
}
