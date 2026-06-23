<?php
namespace CustoDesk\Parser;

use CustoDesk\Parser\Spoiler\SpoilerExtension;
use CustoDesk\Parser\Underline\UnderlineExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownParser
{
    public static function parse(string $source): string
    {
        $environment = new Environment([
            "commonmark" => [
                "use_underscore" => false
            ]
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new StrikethroughExtension());
        $environment->addExtension(new UnderlineExtension());
        $environment->addExtension(new SpoilerExtension());

        $converter = new MarkdownConverter($environment);
        return $converter->convert($source);
    }
}