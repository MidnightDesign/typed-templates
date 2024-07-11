<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

use Stringable;

use function is_string;

final readonly class Token implements Stringable
{
    /**
     * @param TokenType|string $type Strings are used for identifiers
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
    public static function identifier(string $name, int $line, int $column): self
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

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function colon(int $line, int $column): self
    {
        return new self(TokenType::Colon, $line, $column);
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function comma(int $line, int $column): self
    {
        return new self(TokenType::Comma, $line, $column);
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function openSquare(int $line, int $column): self
    {
        return new self(TokenType::OpenSquare, $line, $column);
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function closeSquare(int $line, int $column): self
    {
        return new self(TokenType::CloseSquare, $line, $column);
    }

    public function __toString(): string
    {
        return is_string($this->type) ? $this->type : $this->type->value;
    }
}
