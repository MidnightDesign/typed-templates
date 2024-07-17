<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Template;

use Midnight\TypedTemplates\Parsing\Cursor;
use Midnight\TypedTemplates\Type\AbstractType;
use Midnight\TypedTemplates\Type\Parser\SyntaxError;
use Midnight\TypedTemplates\Type\Parser\TypeParser;

use function array_slice;
use function explode;
use function implode;
use function is_string;
use function trim;

final class TemplateParser
{
    private function __construct()
    {
    }

    public static function parse(string $input): Template|ParseError
    {
        $modelType = TypeParser::parse($input);
        if ($modelType instanceof AbstractType) {
            $lines = explode("\n", $input);
            $lines = array_slice($lines, $modelType->span->end->row);
            $template = implode("\n", $lines);
        } else {
            $template = $input;
            $modelType = null;
        }
        $tokens = new Cursor(Tokenizer::tokenize($template));
        $parts = [];
        while (true) {
            $token = $tokens->current();
            if ($token === null) {
                break;
            }
            $part = match ($token->type) {
                TokenType::OpenCurly => self::parsePlaceholder($tokens),
                TokenType::CloseCurly => $token->type->value,
                default => $token->type,
            };
            $tokens->next();
            $parts[] = $part;
        }
        return new Template($parts, $modelType);
    }

    /**
     * @param Cursor<Token> $tokens
     */
    private static function parsePlaceholder(Cursor $tokens): Placeholder
    {
        $tokens->next();
        $token = $tokens->current();
        if ($token === null) {
            return SyntaxError::create('Expected placeholder name', $tokens->current()->span);
        }
        if (!is_string($token->type)) {
            return SyntaxError::create('Expected placeholder name', $tokens->current()->span);
        }
        $name = trim($token->type);
        $tokens->next();
        if ($tokens->current()->type !== TokenType::CloseCurly) {
            return SyntaxError::create('Expected "}", got ' . $tokens->current(), $tokens->current()->span);
        }
        $tokens->next();
        return new Placeholder(trim($name));
    }
}
