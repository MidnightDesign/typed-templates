<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

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

    private const CLOSE_CURLY = 0x7D;
    private const OPEN_CURLY = 0x7B;

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
            $line = $this->line;
            $column = $this->column;
            $token = match ($char) {
                self::CLOSE_CURLY => Token::closeCurly($line, $column),
                self::OPEN_CURLY => Token::openCurly($line, $column),
                default => Token::raw($this->consumeRaw(), $line, $column),
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

    private function consumeRaw(): string
    {
        $raw = '';
        while (true) {
            $char = $this->peek();
            if ($char === null) {
                break;
            }
            if ($char === self::OPEN_CURLY || $char === self::CLOSE_CURLY) {
                break;
            }
            $raw .= mb_chr($char, self::ENCODING);
            $this->consume();
        }
        return $raw;
    }
}
