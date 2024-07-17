<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Parsing;

use RuntimeException;

use function sprintf;

final class SyntaxError extends RuntimeException
{
    private function __construct(string $message, public readonly Span $span)
    {
        parent::__construct(sprintf("%s at %s", $message, $span));
    }

    public static function create(string $message, Span $span): self
    {
        return new self($message, $span);
    }
}
