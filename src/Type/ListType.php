<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type;

use function sprintf;

final readonly class ListType extends AbstractType
{
    public function __construct(public readonly AbstractType $elementType)
    {
    }

    public function __toString(): string
    {
        return sprintf('[%s]', $this->elementType);
    }
}
