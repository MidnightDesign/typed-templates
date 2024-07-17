<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use Midnight\TypedTemplates\Parsing\Cursor;
use Midnight\TypedTemplates\Parsing\SyntaxError;

use function assert;
use function is_string;
use function trim;

final class TemplateParser
{
    private function __construct()
    {
    }

    public static function parse(string $input): Template|SyntaxError
    {
        $tokens = new Cursor(Tokenizer::tokenize($input));
        $parts = [];
        while (true) {
            $token = $tokens->current();
            if ($token === null) {
                break;
            }
            $part = match ($token->type) {
                TokenType::OpenCurly => self::parsePlaceholder($tokens),
                TokenType::CloseCurly => $token->type->value,
                TokenType::DoubleOpenCurly => '{',
                TokenType::DoubleCloseCurly => '}',
                default => $token->type,
            };
            if ($part instanceof SyntaxError) {
                return $part;
            }
            $tokens->next();
            $parts[] = $part;
        }
        return new Template($parts);
    }

    /**
     * @param Cursor<Token> $tokens
     */
    private static function parsePlaceholder(Cursor $tokens): Placeholder|SyntaxError
    {
        $curly = $tokens->current();
        assert($curly !== null);
        $curlySpan = $curly->span;
        $tokens->next();
        $nameToken = $tokens->current();
        if ($nameToken === null) {
            return SyntaxError::create('Expected placeholder name, got end of input', $curlySpan);
        }
        if (!is_string($nameToken->type)) {
            return SyntaxError::create('Expected placeholder name, got ' . $nameToken->type->value, $nameToken->span);
        }
        $tokens->next();
        $closeCurlyToken = $tokens->current();
        if ($closeCurlyToken === null) {
            return SyntaxError::create('Expected "}", got end of input', $nameToken->span);
        }
        if (is_string($closeCurlyToken->type)) {
            return SyntaxError::create('Expected "}", got ' . $closeCurlyToken->type, $closeCurlyToken->span);
        }
        if ($closeCurlyToken->type !== TokenType::CloseCurly) {
            return SyntaxError::create('Expected "}", got ' . $closeCurlyToken->type->value, $closeCurlyToken->span);
        }
        return new Placeholder(trim($nameToken->type));
    }
}
