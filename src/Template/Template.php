<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use Midnight\TypedTemplates\Type\AbstractType;

final readonly class Template
{
    /**
     * @param list<string | Placeholder> $parts
     * @param AbstractType|null $modelType
     */
    public function __construct(public array $parts, public AbstractType|null $modelType = null)
    {
    }
}
