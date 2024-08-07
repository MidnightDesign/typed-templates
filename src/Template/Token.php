<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use Midnight\TypedTemplates\Parsing\Location;
use Midnight\TypedTemplates\Parsing\Span;
use Stringable;

use function is_string;

final readonly class Token implements Stringable
{
    /**
     * @param TokenType|string $type Strings are used for raw text
     */
    private function __construct(public TokenType|string $type, public Span $span)
    {
    }

    public static function raw(string $name, Span $span): self
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
     * @param positive-int $startColumn
     */
    public static function doubleCloseCurly(int $line, int $startColumn): self
    {
        $span = new Span(new Location($line, $startColumn), new Location($line, $startColumn + 1));
        return new self(TokenType::DoubleCloseCurly, $span);
    }

    /**
     * @param positive-int $line
     * @param positive-int $startColumn
     */
    public static function doubleOpenCurly(int $line, int $startColumn): self
    {
        $span = new Span(new Location($line, $startColumn), new Location($line, $startColumn + 1));
        return new self(TokenType::DoubleOpenCurly, $span);
    }

    /**
     * @param positive-int $line
     * @param positive-int $startColumn
     */
    public static function listIndicator(int $line, int $startColumn): self
    {
        $span = new Span(new Location($line, $startColumn), new Location($line, $startColumn + 1));
        return new self(TokenType::ListIndicator, $span);
    }

    public function __toString(): string
    {
        return is_string($this->type) ? $this->type : $this->type->value;
    }
}
