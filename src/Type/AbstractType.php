<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type;

use Midnight\TypedTemplates\Parsing\Span;
use Stringable;

abstract readonly class AbstractType implements Stringable
{
    public function __construct(public Span $span)
    {
    }
}
