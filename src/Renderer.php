<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates;

use LogicException;
use Midnight\TypedTemplates\Template\Placeholder;
use Midnight\TypedTemplates\Template\Template;
use Midnight\TypedTemplates\Template\TemplateResolverInterface;

use function get_debug_type;
use function is_array;
use function is_object;
use function is_string;
use function sprintf;

final class Renderer
{
    private readonly TemplateResolverInterface $templateResolver;

    public function __construct(TemplateResolverInterface|null $templateResolver = null)
    {
        $this->templateResolver = $templateResolver ?? self::noTemplateResolver();
    }

    private static function noTemplateResolver(): TemplateResolverInterface
    {
        return new class implements TemplateResolverInterface {
            public function resolve(string $class): Template
            {
                throw new LogicException(
                    sprintf('Cannot resolve class %s to a template without a template resolver', $class),
                );
            }
        };
    }

    public function render(Template $template, object $data): string
    {
        $output = '';
        foreach ($template->parts as $part) {
            if (is_string($part)) {
                $output .= $part;
            } else {
                $output .= $this->renderPlaceholder($part, $data);
            }
        }
        return $output;
    }

    private function renderPlaceholder(Placeholder $placeholder, object $model): string
    {
        return $this->renderPlaceholderValue($model->{$placeholder->name});
    }

    private function renderPlaceholderValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_object($value)) {
            return $this->render($this->templateResolver->resolve($value::class), $value);
        }
        if (is_array($value)) {
            $out = '';
            foreach ($value as $item) {
                $out .= $this->renderPlaceholderValue($item);
            }
            return $out;
        }
        throw new LogicException(sprintf("Cannot render value of type %s", get_debug_type($value)));
    }
}
