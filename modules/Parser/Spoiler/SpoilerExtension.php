<?php
declare(strict_types=1);

namespace CustoDesk\Parser\Spoiler;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class SpoilerExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addDelimiterProcessor(new SpoilerDelimiterProcessor());
        $environment->addRenderer(Spoiler::class, new SpoilerRenderer());
    }
}
