<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

enum TokenType: string
{
    case CloseAngle = '>';
    case CloseCurly = '}';
    case CloseSquare = ']';
    case Colon = ':';
    case Comma = ',';
    case OpenAngle = '<';
    case OpenCurly = '{';
    case OpenSquare = '[';
}
