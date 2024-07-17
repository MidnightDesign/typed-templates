<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates\Tests\Functional;

use Midnight\TypedTemplates\Renderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RendererTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string|object|bool|null, string}>
     */
    public static function renderCases(): iterable
    {
        yield 'Just a string' => ["string\n{ model }", 'Test', 'Test'];
        yield 'String with stuff around it' => ["string\nFoo { model } Bar", 'Test', 'Foo Test Bar'];
        yield 'Struct field access' => ["{ foo: string }\n{ model.foo }", (object)['foo' => 'Test'], 'Test'];
    }

    #[DataProvider('renderCases')]
    public function testRender(string $template, string|object|bool|null $data, string $expected): void
    {
        $this->assertSame($expected, (new Renderer())->render($template, $data));
    }
}
