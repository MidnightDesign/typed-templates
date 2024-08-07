<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

enum TokenType: string
{
    case OpenCurly = '{';
    case CloseCurly = '}';
    case DoubleOpenCurly = '{{';
    case DoubleCloseCurly = '}}';
    case ListIndicator = '[]';
}
