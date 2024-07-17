<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

use Midnight\TypedTemplates\Parsing\Span;
use Stringable;

use function is_string;

final readonly class Token implements Stringable
{
    /**
     * @param TokenType|string $type Strings are used for identifiers
     */
    private function __construct(public TokenType|string $type, public Span $span)
    {
    }

    public static function identifier(string $name, Span $span): self
    {
        return new self($name, $span);
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function openCurly(int $line, int $column): self
    {
        return new self(TokenType::OpenCurly, Span::char($line, $column));
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function closeCurly(int $line, int $column): self
    {
        return new self(TokenType::CloseCurly, Span::char($line, $column));
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function colon(int $line, int $column): self
    {
        return new self(TokenType::Colon, Span::char($line, $column));
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function comma(int $line, int $column): self
    {
        return new self(TokenType::Comma, Span::char($line, $column));
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function openSquare(int $line, int $column): self
    {
        return new self(TokenType::OpenSquare, Span::char($line, $column));
    }

    /**
     * @param positive-int $line
     * @param positive-int $column
     */
    public static function closeSquare(int $line, int $column): self
    {
        return new self(TokenType::CloseSquare, Span::char($line, $column));
    }

    public function __toString(): string
    {
        return is_string($this->type) ? $this->type : $this->type->value;
    }
}
