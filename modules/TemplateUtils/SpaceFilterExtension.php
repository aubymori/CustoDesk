<?php
namespace CustoDesk\TemplateUtils;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SpaceFilterExtension extends AbstractExtension
{
    private const MARKER_START = "\x00KEEP_SPACE_START\x00";
    private const MARKER_END   = "\x00KEEP_SPACE_END\x00";

    public function getFilters(): array
    {
        return [
            new TwigFilter('nospace',   [$this, 'nospaceFilter'],   ['is_safe' => ['html']]),
            new TwigFilter('keepspace', [$this, 'keepspaceFilter'], ['is_safe' => ['html']]),
        ];
    }

    public function keepspaceFilter(mixed $value): string
    {
        return self::MARKER_START . $value . self::MARKER_END;
    }

    public function nospaceFilter(mixed $value): string
    {
        $value = (string)$value;
        // 1. Extract every keepspace-marked region and replace it
        //    with a stable placeholder.
        $preserved = [];
        $result = \preg_replace_callback(
            '/' . \preg_quote(self::MARKER_START, '/') . '(.*?)' . \preg_quote(self::MARKER_END, '/') . '/s',
            function (array $matches) use (&$preserved): string {
                $idx = \count($preserved);
                $preserved[$idx] = $matches[1];
                return "\x00PRESERVED{$idx}\x00";
            },
            $value
        );

        // 2. Strip all whitespace from the non-preserved parts.
        $result = \preg_replace('/>\s+</', '><', $result);

        // 3. Put back the preserved regions exactly as they were.
        foreach ($preserved as $idx => $content) {
            $result = \str_replace("\x00PRESERVED{$idx}\x00", $content, $result);
        }

        return $result;
    }
}
