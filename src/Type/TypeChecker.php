<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type;

use function get_debug_type;
use function is_string;
use function sprintf;

final class TypeChecker
{
    private function __construct()
    {
    }

    public static function check(AbstractType $typeNode, mixed $value): true|string
    {
        return match ($typeNode::class) {
            StringType::class => is_string($value) ? true : sprintf('Expected string, got %s', get_debug_type($value)),
        };
    }
}
