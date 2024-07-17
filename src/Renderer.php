<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates;

use Midnight\TypedTemplates\Template\Placeholder;
use Midnight\TypedTemplates\Template\Template;
use Midnight\TypedTemplates\Template\TemplateParser;
use Midnight\TypedTemplates\Type\TypeChecker;

use function count;
use function explode;
use function is_string;

final class Renderer
{
    public function render(string $source, string|object|bool|null $data): string
    {
        $template = TemplateParser::parse($source);
        if (!$template instanceof Template) {
            return $template;
        }
        if ($template->modelType !== null) {
            $typeCheckResult = TypeChecker::check($template->modelType, $data);
            if ($typeCheckResult !== true) {
                return $typeCheckResult;
            }
        }
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

    private function renderPlaceholder(Placeholder $placeholder, object|bool|string|null $model): string
    {
        $path = explode('.', $placeholder->name);
        $value = $model;
        while (true) {
            if (count($path) === 1) {
                return $value;
            }
        }
    }
}
