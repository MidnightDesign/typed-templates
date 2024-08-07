<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

enum PlaceholderType
{
    case String;
    case Template;
    case TemplateList;
}
