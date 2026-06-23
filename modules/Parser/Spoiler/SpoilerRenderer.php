<?php
declare(strict_types=1);

namespace CustoDesk\Parser\Spoiler;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Xml\XmlNodeRendererInterface;

final class SpoilerRenderer implements NodeRendererInterface, XmlNodeRendererInterface
{
    /**
     * @param Spoiler $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable
    {
        Spoiler::assertInstanceOf($node);

        $attrs = $node->data->get('attributes');
        $attrs += [ "class" => "spoiler" ];

        return new HtmlElement('span', $attrs, $childRenderer->renderNodes($node->children()));
    }

    public function getXmlTagName(Node $node): string
    {
        return 'spoiler';
    }

    /**
     * {@inheritDoc}
     */
    public function getXmlAttributes(Node $node): array
    {
        return [];
    }
}
