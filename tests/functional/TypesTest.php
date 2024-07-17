<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Tests\Functional;

use LogicException;
use Midnight\TypedTemplates\Type\Parser\ParseError;
use Midnight\TypedTemplates\Type\Parser\TypeParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function is_string;
use function sprintf;
use function strpos;
use function substr;

final class TypesTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function parseAndToStringCases(): iterable
    {
        $cases = [
            'string',
            '{}',
            '{ foo: string, bar: string }',
            '{ foo: {} }',
            '{ foo: { bar: string } }',
            '[string]',
            '[{}]',
            '[{ foo: string }]',
            '{ foo: [{ bar: [string] }] }',
        ];

        foreach ($cases as $key => $value) {
            if (is_string($key)) {
                yield $key => [$key, $value];
            } else {
                yield $value => [$value];
            }
        }
    }

    /**
     * @return iterable<string, array{string, positive-int, positive-int, string}>
     */
    public static function syntaxErrorCases(): iterable
    {
        $cases = [
            [
                '{{',
                ' ^ Expected identifier, got "{"',
            ],
            [
                '[{]',
                '  ^ Expected identifier, got "]"',
            ],
            [
                'unknowntype',
                '^ Unknown type "unknowntype"',
            ],
        ];

        foreach ($cases as [$type, $expectation]) {
            $row = 1;
            $column = strpos($expectation, '^') + 1;
            $message = sprintf('%s (%d:%d)', substr($expectation, $column + 1), $row, $column);
            yield $type => [$type, $row, $column, $message];
        }
    }

    #[DataProvider('parseAndToStringCases')]
    public function testParseAndToString(string $type, string|null $expected = null): void
    {
        $parsed = TypeParser::parse($type);
        if ($parsed instanceof ParseError) {
            throw new LogicException(sprintf('Failed to parse type "%s": %s', $type, $parsed->getMessage()), previous: $parsed);
        }
        $serialized = (string)$parsed;

        self::assertSame($type, $serialized);
    }

    #[DataProvider('syntaxErrorCases')]
    public function testSyntaxError(string $type, int $expectedRow, int $expectedColumn, string $expectedMessage): void
    {
        $parsed = TypeParser::parse($type);

        self::assertInstanceOf(ParseError::class, $parsed);
        self::assertSame($expectedRow, $parsed->span->start->row, sprintf('Expected start row %d, got %d', $expectedRow, $parsed->span->start->row));
        self::assertSame($expectedColumn, $parsed->span->start->column, sprintf('Expected column %d, got %d', $expectedColumn, $parsed->span->start->column));
        self::assertSame($expectedMessage, $parsed->getMessage());
    }
}
