<?php
declare(strict_types=1);

namespace CustoDesk\Parser\Spoiler;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class SpoilerExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        // Must run before BlockQuoteStartParser (priority 70) so that
        // >! at the start of a line is not consumed as a blockquote.
        $environment->addBlockStartParser(new SpoilerBlockStartParser(), 80);

        $environment->addDelimiterProcessor(new SpoilerDelimiterProcessor());
        $environment->addRenderer(Spoiler::class, new SpoilerRenderer());
    }
}
