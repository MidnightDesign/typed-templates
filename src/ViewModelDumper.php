<?php

declare(strict_types=1);

namespace Midnight\TypedTemplates;

use Midnight\TypedTemplates\Template\Placeholder;
use Midnight\TypedTemplates\Template\PlaceholderType;
use Midnight\TypedTemplates\Template\Template;
use PhpParser\Builder\Class_;
use PhpParser\Builder\Method;
use PhpParser\Builder\Namespace_;
use PhpParser\Builder\Param;
use PhpParser\Builder\Use_;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\UnionType;
use PhpParser\PrettyPrinter\Standard;

use function array_key_exists;
use function array_pop;
use function explode;
use function implode;
use function str_replace;
use function usort;

final readonly class ViewModelDumper
{
    private static function prefixLines(string $prefix, string $text): string
    {
        return $prefix . str_replace("\n", "\n" . $prefix, $text);
    }

    private static function docblockString(string $text): string
    {
        $text = self::prefixLines(' * ', $text);
        return "/**\n" . $text . "\n */";
    }

    private static function compareParts(Placeholder $a, Placeholder $b): int
    {
        $aOptional = $a->getType() !== PlaceholderType::String ? 1 : 0;
        $bOptional = $b->getType() !== PlaceholderType::String ? 1 : 0;
        if ($aOptional !== $bOptional) {
            return $aOptional <=> $bOptional;
        }
        $aList = $a->getType() === PlaceholderType::TemplateList ? 1 : 0;
        $bList = $b->getType() === PlaceholderType::TemplateList ? 1 : 0;
        if ($aList !== $bList) {
            return $bList <=> $aList;
        }
        return $a->name <=> $b->name;
    }

    public function dump(string $fqcn, Template $template): string
    {
        $stmts = [];
        $parts = explode('\\', $fqcn);
        $name = array_pop($parts);
        $namespace = $parts === [] ? null : implode('\\', $parts);
        if ($namespace !== null) {
            $stmts[] = (new Namespace_($namespace))->getNode();
        }
        $params = [];
        $seen = [];
        $docblockTypes = [];
        $parts = $template->placeholders();
        usort($parts, self::compareParts(...));
        foreach ($parts as $part) {
            if (array_key_exists($part->name, $seen)) {
                continue;
            }
            [$nativeType, $docblockType, $default, $imports] = match ($part->getType()) {
                PlaceholderType::String => [
                    'string',
                    null,
                    null,
                    [],
                ],
                PlaceholderType::TemplateList => [
                    'array',
                    'list<' . $part->name . '> $' . $part->name,
                    new ConstFetch(new Name('[]')),
                    [$namespace . '\\' . $name . '\\' . $part->name],
                ],
                PlaceholderType::Template => [
                    new UnionType([new Name($part->name), new Identifier('null')]),
                    null,
                    new ConstFetch(new Name('null')),
                    [$namespace . '\\' . $name . '\\' . $part->name],
                ],
            };
            if ($docblockType !== null) {
                $docblockTypes[] = '@param ' . $docblockType;
            }
            foreach ($imports as $import) {
                $stmts[] = (new Use_($import, \PhpParser\Node\Stmt\Use_::TYPE_NORMAL))->getNode();
            }
            $param = (new Param($part->name))
                ->makePublic()
                ->setType($nativeType);
            if ($default !== null) {
                $param->setDefault($default);
            }
            $params[] = $param;
            $seen[$part->name] = true;
        }
        $constructor = (new Method('__construct'))
            ->makePublic()
            ->addParams($params);
        if ($docblockTypes !== []) {
            $constructor->setDocComment(new Doc(self::docblockString(implode("\n", $docblockTypes))));
        }
        $class = (new Class_($name))
            ->makeFinal()
            ->makeReadonly()
            ->addStmt($constructor);
        $stmts[] = $class->getNode();
        return (new Standard([]))->prettyPrintFile($stmts);
    }
}
