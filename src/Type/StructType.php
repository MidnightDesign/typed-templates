<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type;

use function implode;
use function sprintf;

final readonly class StructType extends AbstractType
{
    /**
     * @param array<string, AbstractType> $fields
     */
    public function __construct(public array $fields)
    {
    }

    public function __toString(): string
    {
        $fields = [];
        foreach ($this->fields as $name => $type) {
            $fields[] = sprintf('%s: %s', $name, $type);
        }
        if ($fields === []) {
            return '{}';
        }
        return sprintf("{ %s }", implode(', ', $fields));
    }
}
