<?php
namespace CustoDesk\Page\Debug;

use CustoDesk\Parser\BBCodeParser;
use CustoDesk\Parser\MarkdownParser;
use CustoDesk\RequestMetadata;

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
        $html = $_POST["rich_edit_text"];
        $this->data->source = $html;

        switch ($_POST["rich_edit_choice"])
        {
            case "bbcode":
                $html = BBCodeParser::parse($html);
                break;
            case "markdown":
                $html = MarkdownParser::parse($html);
                break;
            case "html":
                $html = htmlspecialchars($_POST["rich_edit_text"]);
                break;
        }

        $this->data->html = $html;
        return true;
    }
}