<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

enum TokenType: string
{
    case CloseCurly = '}';
    case OpenCurly = '{';
}
