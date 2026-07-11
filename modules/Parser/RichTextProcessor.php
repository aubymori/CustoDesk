<?php
namespace CustoDesk\Parser;

use CustoDesk\Cookie;
use HTMLPurifier;
use HTMLPurifier_Config;

class RichTextProcessor
{
    /* Returns FALSE for empty text. */
    public static function processRichText(?object $data = null): object
    {
        $html = trim($_POST["rich_edit_text"]);
        $source = trim($html);

        if (@$_POST["rich_edit_choice"] == "markdown")
        {
            $html = MarkdownParser::parse($html);
            $choice = "markdown";
        }
        else
        {
            $html = BBCodeParser::parse($html);
            $choice = "bbcode";
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set("HTML.Allowed", "*[style],span[class],div,a[href],img[src|alt],p,h1,h2,h3,b,strong,i,em,u,strike,del,sup,sub,pre,code,hr,ul,ol,li,br,blockquote");
        $purifier = new HTMLPurifier($config);
        $html = $purifier->purify($html);

        if ($data != null)
        {
            $data->rich_edit_source = $source;
            $data->rich_edit_choice = $choice;
        }

        Cookie::set("rich_edit_choice", $choice);

        return (object)[
            "source" => $source,
            "html" => trim($html),
            "editor" => $choice
        ];
    }
}