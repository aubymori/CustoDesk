<?php
declare(strict_types=1);

namespace CustoDesk\Parser\Spoiler;

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Delimiter\Processor\CacheableDelimiterProcessorInterface;
use League\CommonMark\Node\Inline\AbstractStringContainer;
use League\CommonMark\Node\Inline\Text;

final class SpoilerDelimiterProcessor implements CacheableDelimiterProcessorInterface
{
    public function getOpeningCharacter(): string
    {
        return '!';
    }

    public function getClosingCharacter(): string
    {
        return '!';
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        if ($opener->getLength() !== 1 || $closer->getLength() !== 1) {
            return 0;
        }

        // Validate that the opening ! is preceded by >
        $openerValid = false;
        $openerNode = $opener->getInlineNode();
        $prev = $openerNode->previous();

        if ($prev === null) {
            // At the very start of inline content — valid (handles the start-of-line case)
            $openerValid = true;
        } elseif ($prev instanceof Text && \str_ends_with($prev->getLiteral(), '>')) {
            // Preceded by a > character
            $openerValid = true;
        }

        if (! $openerValid) {
            return 0;
        }

        // Validate that the closing ! is followed by <
        $closerValid = false;
        $closerNode = $closer->getInlineNode();
        $next = $closerNode->next();

        if ($next instanceof Text && \str_starts_with($next->getLiteral(), '<')) {
            $closerValid = true;
        }

        if (! $closerValid) {
            return 0;
        }

        return 1;
    }

    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse): void
    {
        $spoiler = new Spoiler();

        // Move all children between opener and closer into the Spoiler node
        $tmp = $opener->next();
        while ($tmp !== null && $tmp !== $closer) {
            $next = $tmp->next();
            $spoiler->appendChild($tmp);
            $tmp = $next;
        }

        // Strip the '>' that precedes the opening ! (part of the >! syntax)
        $prev = $opener->previous();
        if ($prev instanceof Text) {
            $literal = $prev->getLiteral();
            if (\str_ends_with($literal, '>')) {
                $literal = \substr($literal, 0, -1);
                if ($literal === '') {
                    $prev->detach();
                } else {
                    $prev->setLiteral($literal);
                }
            }
        }

        // Strip the '<' that follows the closing ! (part of the !< syntax)
        $next = $closer->next();
        if ($next instanceof Text) {
            $literal = $next->getLiteral();
            if (\str_starts_with($literal, '<')) {
                $literal = \substr($literal, 1);
                if ($literal === '') {
                    $next->detach();
                } else {
                    $next->setLiteral($literal);
                }
            }
        }

        $opener->insertAfter($spoiler);
    }

    public function getCacheKey(DelimiterInterface $closer): string
    {
        return '!';
    }
}
