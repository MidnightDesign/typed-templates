<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use Midnight\TypedTemplates\Parsing\Location;
use Midnight\TypedTemplates\Parsing\Span;

use function current;
use function in_array;
use function mb_chr;
use function mb_ord;
use function mb_str_split;
use function next;

/**
 * @internal
 */
final class Tokenizer
{
    public const ENCODING = 'UTF-8';

    private const CLOSE_CURLY = 0x7D;
    private const OPEN_CURLY = 0x7B;
    private const OPEN_SQUARE = 0x5B;
    private const CLOSE_SQUARE = 0x5D;

    /** @var positive-int */
    private int $line = 1;
    /** @var positive-int */
    private int $column = 1;

    /**
     * @param list<string> $chars
     */
    private function __construct(private array $chars)
    {
    }

    /**
     * @return iterable<Token>
     */
    public static function tokenize(string $source): iterable
    {
        $chars = mb_str_split($source);
        return (new self($chars))->doLex();
    }

    /**
     * @return iterable<Token>
     */
    private function doLex(): iterable
    {
        while (true) {
            $char = $this->peek();
            if ($char === null) {
                break;
            }
            yield match ($char) {
                self::CLOSE_CURLY => $this->closeCurly(),
                self::OPEN_CURLY => $this->openCurly(),
                self::OPEN_SQUARE => $this->openSquare(),
                default => $this->raw(),
            };
        }
    }

    private function peek(): int|null
    {
        $char = current($this->chars);
        if ($char === false) {
            return null;
        }
        return mb_ord($char, self::ENCODING);
    }

    private function consume(): void
    {
        $char = current($this->chars);
        if ($char === false) {
            return;
        }
        $this->column++;
        if ($char === "\n") {
            $this->line++;
            $this->column = 1;
        }
        next($this->chars);
    }

    /**
     * @return array{string, Span}
     */
    private function consumeRaw(): array
    {
        $start = new Location($this->line, $this->column);
        $end = $start;
        $raw = '';
        while (true) {
            $char = $this->peek();
            if ($char === null) {
                break;
            }
            if (in_array($char, [self::OPEN_CURLY , self::CLOSE_CURLY, self::OPEN_SQUARE, self::CLOSE_SQUARE], true)) {
                break;
            }
            $raw .= mb_chr($char, self::ENCODING);
            $end = new Location($this->line, $this->column);
            $this->consume();
        }
        return [$raw, new Span($start, $end)];
    }

    private function closeCurly(): Token
    {
        $line = $this->line;
        $column = $this->column;
        $this->consume();
        if ($this->peek() !== self::CLOSE_CURLY) {
            return Token::closeCurly($line, $column);
        }
        $this->consume();
        return Token::doubleCloseCurly($line, $column);
    }

    private function openCurly(): Token
    {
        $line = $this->line;
        $column = $this->column;
        $this->consume();
        if ($this->peek() !== self::OPEN_CURLY) {
            return Token::openCurly($line, $column);
        }
        $this->consume();
        return Token::doubleOpenCurly($line, $column);
    }

    private function openSquare(): Token
    {
        $line = $this->line;
        $column = $this->column;
        $this->consume();
        if ($this->peek() !== self::CLOSE_SQUARE) {
            return Token::raw('[', Span::char($line, $column));
        }
        $this->consume();
        return Token::listIndicator($this->line, $this->column);
    }

    private function raw(): Token
    {
        [$raw, $span] = $this->consumeRaw();
        return Token::raw($raw, $span);
    }
}
