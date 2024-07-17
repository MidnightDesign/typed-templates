<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

use Midnight\TypedTemplates\Parsing\Span;
use RuntimeException;

use function sprintf;

abstract class ParseError extends RuntimeException
{
    final private function __construct(string $message, public readonly Span $span)
    {
        parent::__construct(sprintf('%s (%s)', $message, $span));
    }

    /**
     * @param positive-int $row
     * @param positive-int $column
     */
    public static function create(string $message, Span $span): static
    {
        return new static($message, $span);
    }
}
