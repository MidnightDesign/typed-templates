<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type;

final readonly class StringType extends AbstractType
{
    public function __toString(): string
    {
        return 'string';
    }
}
