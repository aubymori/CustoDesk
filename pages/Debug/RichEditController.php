<?php
namespace CustoDesk\Page\Debug;

use CustoDesk\Parser\BBCodeParser;
use CustoDesk\Parser\MarkdownParser;
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
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set("HTML.Allowed", "*[style],span[class],div,a[href],img[src|alt],p,h1,h2,h3,b,strong,i,em,u,strike,del,sup,sub,pre,code,hr,ul,ol,li,br,blockquote");
        $purifier = new HTMLPurifier($config);
        $html = $purifier->purify($html);

        $this->data->html = $html;
        return true;
    }
}