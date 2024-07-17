<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

interface TemplateResolverInterface
{
    /**
     * @param class-string $class
     */
    public function resolve(string $class): Template;
}
