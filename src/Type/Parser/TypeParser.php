<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Type\Parser;

use Midnight\TypedTemplates\Parsing\Cursor;
use Midnight\TypedTemplates\Type\AbstractType;
use Midnight\TypedTemplates\Type\ListType;
use Midnight\TypedTemplates\Type\StringType;
use Midnight\TypedTemplates\Type\StructType;

use function assert;
use function is_string;
use function sprintf;

final class TypeParser
{
    private function __construct()
    {
    }

    public static function parse(string $input): AbstractType|ParseError
    {
        $tokens = new Cursor(Tokenizer::tokenize($input));
        return self::parseType($tokens);
    }

    /**
     * @param Cursor<Token> $tokens
     */
    private static function parseType(Cursor $tokens): AbstractType|ParseError
    {
        return match ($tokens->current()->type) {
            TokenType::OpenCurly => self::parseStruct($tokens),
            TokenType::OpenSquare => self::parseList($tokens),
            default => self::parseIdentifierType($tokens),
        };
    }

    /**
     * @param Cursor<Token> $tokens
     */
    private static function parseStruct(Cursor $tokens): StructType|ParseError
    {
        $tokens->next();
        $fields = [];
        while (true) {
            $token = $tokens->current();
            if ($token->type === TokenType::CloseCurly) {
                $tokens->next();
                return new StructType($fields);
            }
            if (!is_string($token->type)) {
                return SyntaxError::create(sprintf('Expected identifier, got "%s"', $token), $token->span);
            }
            $name = $token->type;
            $token = $tokens->next();
            if ($token->type !== TokenType::Colon) {
                return SyntaxError::create(sprintf('Expected ":", got "%s"', $token), $token->line, $token->column);
            }
            $tokens->next();
            $type = self::parseType($tokens);
            if ($type instanceof ParseError) {
                return $type;
            }
            $fields[$name] = $type;
            $token = $tokens->current();
            if ($token->type !== TokenType::Comma) {
                continue;
            }
            $tokens->next();
        }
    }

    /**
     * @param Cursor<Token> $tokens
     */
    private static function parseIdentifierType(Cursor $tokens): AbstractType|ParseError
    {
        $token = $tokens->current();
        assert(is_string($token->type));
        if ($token->type === 'string') {
            $tokens->next();
            return new StringType($token->span);
        }
        return TypeError::create(sprintf('Unknown type "%s"', $token->type), $token->span);
    }

    /**
     * @param Cursor<Token> $tokens
     */
    private static function parseList(Cursor $tokens): ListType|ParseError
    {
        $tokens->next();
        $type = self::parseType($tokens);
        if ($type instanceof ParseError) {
            return $type;
        }
        $token = $tokens->current();
        if ($token->type !== TokenType::CloseSquare) {
            return SyntaxError::create(sprintf('Expected "]", got "%s"', $token), $token->line, $token->column);
        }
        $tokens->next();
        return new ListType($type);
    }
}
