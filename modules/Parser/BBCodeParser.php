<?php
namespace CustoDesk\Parser;

use Nbbc\BBCode;

class BBCodeParser
{
    public static function parse(string $source): string
    {
        $bbcode = new BBCode();
        $bbcode->setEnableSmileys(false);
        $bbcode->removeRule("columns");
        $bbcode->removeRule("nextcol");
        $bbcode->removeRule("rtl");
        $bbcode->removeRule("indent");
        $bbcode->removeRule("acronym");
        $bbcode->removeRule("wiki");

        // The library has this, but we want our own class.
        $bbcode->addRule("spoiler", [
            "simple_start" => "<span class=\"spoiler\">",
            "simple_end" => "</span>",
            "class" => "inline",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
        ]);

        // Let's have an inline code rule.
        $bbcode->addRule("code", [
            "simple_start" => "<code>",
            "simple_end" => "</code>",
            "class" => "inline",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
        ]);

        // And one for formatted code.
        $bbcode->addRule("pre", [
            "mode" => BBCode::BBCODE_MODE_ENHANCED,
            "template" => "<pre>{\$_content/v}</pre>",
            "class" => "code",
            "allow_in" => ["listitem", "block", "columns"],
            "content" => BBCode::BBCODE_VERBATIM,
            "before_tag" => "sns",
            "after_tag" => "sn",
            "before_endtag" => "sn",
            "after_endtag" => "sns",
        ]);

        // Make the u rule not use the deprecated <u> element.
        $bbcode->addRule("u", [
            "simple_start" => "<span style=\"text-decoration:underline\">",
            "simple_end" => "</span>",
            "class" => "inline",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
            "plain_start" => "<u>",
            "plain_end" => "</u>",
            "allow_params" => false,
        ]);

        return $bbcode->parse($source);
    }
}