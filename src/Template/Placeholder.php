<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use Stringable;

use function ord;
use function sprintf;

final readonly class Placeholder implements Stringable
{
    private const LOWERCASE_A = 97;
    private const LOWERCASE_Z = 122;

    private function __construct(public string $name, public bool $list)
    {
    }

    public static function create(string $name): self
    {
        return new self($name, false);
    }

    public static function list(string $name): self
    {
        return new self($name, true);
    }

    private static function isLowercase(int $ord): bool
    {
        return $ord >= self::LOWERCASE_A && $ord <= self::LOWERCASE_Z;
    }

    public function __toString(): string
    {
        return $this->list ? sprintf("%s[]", $this->name) : $this->name;
    }

    public function isString(): bool
    {
        return self::isLowercase(ord($this->name[0]));
    }

    public function getType(): PlaceholderType
    {
        if ($this->isString()) {
            return PlaceholderType::String;
        }
        if ($this->list) {
            return PlaceholderType::TemplateList;
        }
        return PlaceholderType::Template;
    }
}
