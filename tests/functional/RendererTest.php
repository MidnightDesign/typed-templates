<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Tests\Functional;

use Midnight\TypedTemplates\Parsing\SyntaxError;
use Midnight\TypedTemplates\Renderer;
use Midnight\TypedTemplates\Template\Template;
use Midnight\TypedTemplates\Template\TemplateParser;
use Midnight\TypedTemplates\Template\TemplateResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RendererTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string, 1: object, 2: string, 3?: TemplateResolverInterface}>
     */
    public static function renderCases(): iterable
    {
        yield 'Just a string placeholder' => [
            "{ name }",
            (object)['name' => 'Test'],
            'Test',
        ];
        yield 'String with stuff around it' => [
            "Foo { name } Bar",
            (object)['name' => 'Test'],
            'Foo Test Bar',
        ];
        yield 'Multiple placeholders' => [
            "{ foo } { bar }",
            (object)['foo' => 'Foo', 'bar' => 'Bar'],
            'Foo Bar',
        ];
        yield 'Sub-template' => [
            '{ price }',
            (object)['price' => (object)['amount' => '1.23', 'currency' => 'USD']],
            '1.23 USD',
            self::simpleTemplateResolver('{ amount } { currency }'),
        ];
        yield 'String array' => [
            '<ul>{ users }</ul>',
            (object)['users' => [(object)['name' => 'Alice'], (object)['name' => 'Bob']]],
            '<ul><li>Alice</li><li>Bob</li></ul>',
            self::simpleTemplateResolver('<li>{ name }</li>'),
        ];
        yield 'Escaped curly braces' => [
            '{{ name }}',
            (object)['name' => 'Test'],
            '{ name }',
        ];
    }

    private static function simpleTemplateResolver(string $template): TemplateResolverInterface
    {
        $parsedTemplate = TemplateParser::parse($template);
        if ($parsedTemplate instanceof SyntaxError) {
            throw $parsedTemplate;
        }
        return new class ($parsedTemplate) implements TemplateResolverInterface {
            public function __construct(private readonly Template $template)
            {
            }

            public function resolve(string $class): Template
            {
                return $this->template;
            }
        };
    }

    #[DataProvider('renderCases')]
    public function testRender(
        string $template,
        object $data,
        string $expected,
        TemplateResolverInterface|null $templateResolver = null,
    ): void {
        $templateObject = TemplateParser::parse($template);
        if ($templateObject instanceof SyntaxError) {
            throw $templateObject;
        }
        $this->assertSame($expected, (new Renderer($templateResolver))->render($templateObject, $data));
    }
}
