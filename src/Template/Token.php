<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use Stringable;

use function is_string;

final readonly class Token implements Stringable
{
    /**
     * @param TokenType|string $type Strings are used for raw text
     * @param positive-int $line
     * @param positive-int $column
     */
    private function __construct(public TokenType|string $type, public int $line, public int $column)
    {
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function raw(string $name, int $line, int $column): self
    {
        return new self($name, $line, $column);
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function openCurly(int $line, int $column): self
    {
        return new self(TokenType::OpenCurly, $line, $column);
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function closeCurly(int $line, int $column): self
    {
        return new self(TokenType::CloseCurly, $line, $column);
    }

    public function __toString(): string
    {
        return is_string($this->type) ? $this->type : $this->type->value;
    }
}
