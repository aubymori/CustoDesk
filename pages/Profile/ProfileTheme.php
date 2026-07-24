<?php
namespace CustoDesk\Page\Profile;

use CustoDesk\DB;

class ProfileTheme
{
    public const BG_REPEAT_X     = (1 << 0);
    public const BG_REPEAT_Y     = (1 << 1);

    public const BG_ALIGN_TOP    = 1;
    public const BG_ALIGN_BOTTOM = 2;
    public const BG_ALIGN_LEFT   = 1;
    public const BG_ALIGN_RIGHT  = 2;
    public const BG_ALIGN_CENTER = 3;

    public const COLOR_REGEX = "/^#[0-9A-F]{6}$/i";

    public bool $lightCards = false;
    public bool $darkBg = false;
    public ?string $cardColor = null;
    public ?string $linkColor = null;
    public ?string $background = null;

    public ?string $bgColor = null;
    public ?string $bgImage = null;
    public bool $bgFixed = false;
    public ?string $bgAlignX = null;
    public ?string $bgAlignY = null;
    public bool $bgRepeatX = false;
    public bool $bgRepeatY = false;
    
    private static function isDark(string $color): bool
    {
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));
        $luminance = ($r * 2 + $g * 5 + $b) / 8;
        return ($luminance <= 128);
    }

    public function __construct(int $userId)
    {
        $result = DB::querySingle("SELECT * from user_profiles WHERE user_id=:id", [
            "id" => $userId
        ]);
        if ($result == null)
            return;

        if (null !== $result->card_color)
        {
            $this->cardColor = $result->card_color;
            $this->lightCards = !self::isDark($this->cardColor);
        }

        if (null !== $result->link_color)
        {
            $this->linkColor = $result->link_color;
        }

        $bg = [];

        if (null !== $result->bg_color)
        {
            $bg[] = $result->bg_color;
            $this->bgColor = $result->bg_color;
            $this->darkBg = self::isDark($result->bg_color);
        }

        if (null !== $result->bg_repeat)
        {
            $bg[] = match($result->bg_repeat)
            {
                0                                       => "no-repeat",
                self::BG_REPEAT_X                       => "repeat-x",
                self::BG_REPEAT_Y                       => "repeat-y",
                (self::BG_REPEAT_X | self::BG_REPEAT_Y) => "repeat",
                default                                 => "no-repeat"
            };

            if ($result->bg_repeat & self::BG_REPEAT_X)
            {
                $this->bgRepeatX = true;
            }
            if ($result->bg_repeat & self::BG_REPEAT_Y)
            {
                $this->bgRepeatY = true;
            }
        }

        if (null !== $result->bg_image)
        {
            $bg[] = "url(/user_bgs/" . $result->bg_image . ")";
            $this->bgImage = "/user_bgs/" . $result->bg_image;
        }

        if (1 == $result->bg_fixed)
        {
            $bg[] = "fixed";
            $this->bgFixed = true;
        }

        if (null != $result->bg_align_y)
        {
            $this->bgAlignY = match ($result->bg_align_y)
            {
                self::BG_ALIGN_TOP    => "top",
                self::BG_ALIGN_BOTTOM => "bottom",
                self::BG_ALIGN_CENTER => "center",
                default               => "top",
            };
            $bg[] = $this->bgAlignY;
        }

        if (null != $result->bg_align_x)
        {
            $this->bgAlignX = match ($result->bg_align_x)
            {
                self::BG_ALIGN_LEFT   => "left",
                self::BG_ALIGN_RIGHT  => "right",
                self::BG_ALIGN_CENTER => "center",
                default               => "left",
            };
            $bg[] = $this->bgAlignX;
        }

        if (count($bg) > 0)
        {
            $this->background = implode(" ", $bg);
        }
    }
}