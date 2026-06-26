<?php
namespace CustoDesk\Parser;

use Nbbc\BBCode;

class BBCodeParser
{
    public static function doImage(BBCode $bbcode, int $action, string $name, string $default, array $params, string $content)
    {
        if ($action == BBCode::BBCODE_CHECK)
        {
            return true;
        }
        
        $content = trim($bbcode->unHTMLEncode($content));
        $default = trim($bbcode->unHTMLEncode($default));

        if (empty($default))
        {
            return "<img src=\"" . htmlspecialchars($content, ENT_QUOTES) . "\">";
        }
        else
        {
            return "<img src=\"" . htmlspecialchars($content, ENT_QUOTES) . "\" alt=\"" . htmlspecialchars($default, ENT_QUOTES) . "\">";
        }
    }

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

        // noparse rule
        $bbcode->addRule("noparse", [
            "mode" => BBCode::BBCODE_MODE_ENHANCED,
            "template" => "{\$_content/v}",
            "class" => "code",
            "allow_in" => ["listitem", "block", "columns"],
            "content" => BBCode::BBCODE_VERBATIM,
            "after_tag" => "sn",
            "before_endtag" => "sn",
        ]);

        // Let's have an inline code rule.
        $bbcode->addRule("code", [
            "mode" => BBCode::BBCODE_MODE_ENHANCED,
            "template" => "<code>{\$_content/v}</code>",
            "class" => "code",
            "allow_in" => ["listitem", "block", "columns"],
            "content" => BBCode::BBCODE_VERBATIM,
            "after_tag" => "sn",
            "before_endtag" => "sn",
        ]);

        // And one for formatted code.
        $bbcode->addRule("pre", [
            "mode" => BBCode::BBCODE_MODE_ENHANCED,
            "template" => "<pre>{\$_content/v}</pre>",
            "class" => "code",
            "allow_in" => ["listitem", "block", "columns"],
            "content" => BBCode::BBCODE_VERBATIM,
            "after_tag" => "sn",
            "before_endtag" => "sn",
            "after_endtag" => "sn",
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

        // Replace the rigid quote rule.
        $bbcode->addRule("quote", [
            "simple_start" => "<blockquote>",
            "simple_end" => "</blockquote>",
            "class" => "inline",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
        ]);

        // Header rules
        $bbcode->addRule("h1", [
            "simple_start" => "<h1>",
            "simple_end" => "</h1>",
            "class" => "inline",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
            "before_tag" => "n",
            "after_endtag" => "n",
        ]);
        $bbcode->addRule("h2", [
            "simple_start" => "<h2>",
            "simple_end" => "</h2>",
            "class" => "inline",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
            "before_tag" => "n",
            "after_endtag" => "n",
        ]);
        $bbcode->addRule("h3", [
            "simple_start" => "<h3>",
            "simple_end" => "</h3>",
            "class" => "inline",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
            "before_tag" => "n",
            "after_endtag" => "n",
        ]);

        $bbcode->addRule("img", [
            "mode" => BBCode::BBCODE_MODE_CALLBACK,
            "method" => self::class . "::doImage",
            "class" => "image",
            "allow_in" => ["listitem", "block", "columns", "inline", "link"],
            "end_tag" => BBCode::BBCODE_REQUIRED,
            "content" => BBCode::BBCODE_OPTIONAL,
            "plain_start" => "[image]",
            "plain_content" => [],
        ]);

        return $bbcode->parse($source);
    }
}