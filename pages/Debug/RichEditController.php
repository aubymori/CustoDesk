<?php
namespace CustoDesk\Page\Debug;

use CustoDesk\Parser\BBCodeParser;
use CustoDesk\Parser\MarkdownParser;
use CustoDesk\Parser\RichTextProcessor;
use CustoDesk\RequestMetadata;
use HTMLPurifier;
use HTMLPurifier_Config;

class RichEditController extends DebugPageController
{
    public string $template = "debug/rich_edit";
    public string $title = "Rich Edit Debug";

    public function onGet(RequestMetadata $request): bool
    {
        return true;
    }
    
    public function onPost(RequestMetadata $request): bool
    {   
        $rich = RichTextProcessor::processRichText($this->data);
        $this->data->html = $rich->html;
        return true;
    }
}