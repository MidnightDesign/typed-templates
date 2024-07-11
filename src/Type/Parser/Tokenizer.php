<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

use function current;
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

    private const CLOSE_SQUARE = 0x5D;
    private const CLOSE_CURLY = 0x7D;
    private const COLON = 0x3A;
    private const COMMA = 0x2C;
    private const LOWER_A = 0x61;
    private const LOWER_Z = 0x7A;
    private const NEWLINE = 0x0A;
    private const OPEN_SQUARE = 0x5B;
    private const OPEN_CURLY = 0x7B;
    private const SPACE = 0x20;
    private const TAB = 0x09;
    private const UPPER_A = 0x41;
    private const UPPER_Z = 0x5A;

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

    public static function tokenize(string $source): iterable
    {
        $chars = mb_str_split($source);
        return (new self($chars))->doLex();
    }

    private static function isWhitespace(int $char): bool
    {
        return $char === self::SPACE || $char === self::TAB || $char === self::NEWLINE;
    }

    private static function isIdentifierChar(int $char): bool
    {
        return ($char >= self::LOWER_A && $char <= self::LOWER_Z)
            || ($char >= self::UPPER_A && $char <= self::UPPER_Z);
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
            if (self::isWhitespace($char)) {
                $this->consumeWhitespace();
                continue;
            }
            $line = $this->line;
            $column = $this->column;
            $token = match ($char) {
                self::CLOSE_CURLY => Token::closeCurly($line, $column),
                self::COLON => Token::colon($line, $column),
                self::COMMA => Token::comma($line, $column),
                self::OPEN_CURLY => Token::openCurly($line, $column),
                self::OPEN_SQUARE => Token::openSquare($line, $column),
                self::CLOSE_SQUARE => Token::closeSquare($line, $column),
                default => Token::identifier($this->consumeIdentifier(), $line, $column),
            };
            if ($token->type instanceof TokenType) {
                $this->consume();
            }
            yield $token;
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

    private function consumeWhitespace(): void
    {
        while (true) {
            $char = $this->peek();
            if ($char === null || !self::isWhitespace($char)) {
                break;
            }
            $this->consume();
        }
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

    private function consumeIdentifier(): string
    {
        $identifier = '';
        while (true) {
            $char = $this->peek();
            if ($char === null) {
                break;
            }
            if (!self::isIdentifierChar($char)) {
                break;
            }
            $identifier .= mb_chr($char, self::ENCODING);
            $this->consume();
        }
        return $identifier;
    }
}
