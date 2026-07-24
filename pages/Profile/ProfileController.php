<?php
namespace CustoDesk\Page\Profile;

use CustoDesk\DB;
use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\Util\UserUtils;
use Imagick;
use function CustoDesk\rootpath;

class ProfileController extends PageController
{
    public string $template = "profile/main";
    private int $userId;
    private bool $isSelf;

    private function setUp(RequestMetadata $request): bool
    {
        $username = $request->path[1];
        $user = User::fromUsername($username);
        if ($user == null)
        {
            return false;
        }

        $this->data->user = $user;
        $this->title = $user->username;
        
        $this->userId = $user->id;
        $this->isSelf = Session::getUserId() == $this->userId;
        $this->data->isSelf = $this->isSelf;
        $this->data->isFollowing = UserUtils::isFollowingUser($this->userId);
        $this->data->theme = new ProfileTheme($this->userId);

        return true;
    }

    public function onGet(RequestMetadata $request): bool
    {
        if (!$this->setUp($request))
        {
            return false;
        }

        $this->doMainPage();
        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        if (!$this->setUp($request))
        {
            return false;
        }

        if ($this->getPostValue("action") != "edit_profile")
        {
            goto fail;
        }

        $cardColor = null;
        if ($this->getCheckValue("use_card_color"))
        {
            $cardColor = $this->getPostValue("card_color");
            if (false === preg_match(ProfileTheme::COLOR_REGEX, $cardColor))
            {
                $cardColor = null;
            }
        }

        $linkColor = null;
        if ($this->getCheckValue("use_link_color"))
        {
            $linkColor = $this->getPostValue("link_color");
            if (false === preg_match(ProfileTheme::COLOR_REGEX, $linkColor))
            {
                $linkColor = null;
            }
        }

        $bgColor = null;
        $bgImage = null;
        $bgFixed = null;
        $bgRepeat = null;
        $bgAlignX = null;
        $bgAlignY = null;

        if ($this->getCheckValue("use_bg"))
        {
            $bgColor = $_POST["bg_color"];
            if (false === preg_match(ProfileTheme::COLOR_REGEX, $bgColor))
            {
                $bgColor = "#000000";
            }

            $updateBg = false;

            if (isset($_FILES["bg_image"]) && $_FILES["bg_image"]["error"] != UPLOAD_ERR_NO_FILE)
            {
                $err = $_FILES["bg_image"]["error"];
                if ($err == UPLOAD_ERR_FORM_SIZE
                || $err == UPLOAD_ERR_INI_SIZE)
                {
                    $this->addAlert(AlertType::ERROR, "That background image is too large. It must be at most 5MB.", dismissible: true);
                    goto fail;
                }

                if ($err != 0)
                {
                    $this->addAlert(AlertType::ERROR, "An error occurred while uploading the background image.", dismissible: true);
                    goto fail;
                }

                $tmpPath = $_FILES["bg_image"]["tmp_name"];
                try
                {
                    $im = new Imagick($tmpPath);
                    $im = $im->coalesceImages();
                }
                catch (\Throwable $e)
                {
                    $this->addAlert(AlertType::ERROR, "The uploaded background image is not an image.", dismissible: true);
                    goto fail;
                }

                $ext = match ($im->getImageMimeType())
                {
                    "image/x-gif"  => ".gif",
                    "image/gif"    => ".gif",
                    "image/x-jpeg" => ".jpg",
                    "image/jpeg"   => ".jpg",
                    default        => ".png"
                };

                $bgImage = md5($this->userId . "_" . md5($im, true)) . $ext;
                
                if (!$im->deconstructImages()->writeImages(rootpath("user_bgs/$bgImage"), true))
                {
                    $this->addAlert(AlertType::ERROR, "Failed to write the uploaded background image.", dismissible: true);
                    goto fail;
                }

                $updateBg = true;
            }
            else if ($this->getPostValue("remove_bg") == "1")
            {
                $updateBg = true;
            }

            // Delete old image
            $result = DB::querySingle("SELECT bg_image FROM user_profiles WHERE user_id = :id", [
                "id" => $this->userId
            ]);
            if ($updateBg)
            {
                if ($result != null && $result->bg_image != null && $result->bg_image != $bgImage)
                {
                    unlink(rootpath("user_bgs/" . $result->bg_image));
                }
            }
            else
            {
                $bgImage = ($result == null) ? null : $result->bg_image;
            }
            
            $bgFixed = $this->getCheckValue("bg_fixed");

            $bgAlignX = match ($this->getPostValue("bg_align_x"))
            {
                "left"   => ProfileTheme::BG_ALIGN_LEFT,
                "right"  => ProfileTheme::BG_ALIGN_RIGHT,
                "center" => ProfileTheme::BG_ALIGN_CENTER,
                default  => ProfileTheme::BG_ALIGN_LEFT
            };

            $bgAlignY = match ($this->getPostValue("bg_align_y"))
            {
                "top"    => ProfileTheme::BG_ALIGN_TOP,
                "bottom" => ProfileTheme::BG_ALIGN_BOTTOM,
                "center" => ProfileTheme::BG_ALIGN_CENTER,
                default  => ProfileTheme::BG_ALIGN_TOP
            };

            $bgRepeat = 0;
            if ($this->getCheckValue("bg_repeat_x"))
            {
                $bgRepeat |= ProfileTheme::BG_REPEAT_X;
            }
            if ($this->getCheckValue("bg_repeat_y"))
            {
                $bgRepeat |= ProfileTheme::BG_REPEAT_Y;
            }
        }
        else
        {
            $result = DB::querySingle("SELECT bg_image FROM user_profiles WHERE user_id = :id", [
                "id" => $this->userId
            ]);
            if ($result != null && $result->bg_image != null)
            {
                unlink(rootpath("user_bgs/" . $result->bg_image));
            }
        }

        $result = DB::querySingle("SELECT user_id FROM user_profiles WHERE user_id=:id", [
            "id" => $this->userId
        ]);
        if ($result != null)
        {
            DB::exec("UPDATE user_profiles SET card_color=:card_color, link_color=:link_color, bg_color=:bg_color, bg_image=:bg_image, bg_fixed=:bg_fixed, bg_repeat=:bg_repeat, bg_align_x=:bg_align_x, bg_align_y=:bg_align_y WHERE user_id=:id", [
                "id" => $this->userId,
                "card_color" => $cardColor,
                "link_color" => $linkColor,
                "bg_color"   => $bgColor,
                "bg_image"   => $bgImage,
                "bg_fixed"   => $bgFixed,
                "bg_repeat"  => $bgRepeat,
                "bg_align_x" => $bgAlignX,
                "bg_align_y" => $bgAlignY,
            ]);
        }
        else
        {
            DB::exec("INSERT INTO user_profiles (user_id, card_color, link_color, bg_color, bg_image, bg_fixed, bg_repeat, bg_align_x, bg_align_y) VALUES (:id, :card_color, :link_color, :bg_color, :bg_image, :bg_fixed, :bg_repeat, :bg_align_x, :bg_align_y)", [
                "id" => $this->userId,
                "card_color" => $cardColor,
                "link_color" => $linkColor,
                "bg_color"   => $bgColor,
                "bg_image"   => $bgImage,
                "bg_fixed"   => $bgFixed,
                "bg_repeat"  => $bgRepeat,
                "bg_align_x" => $bgAlignX,
                "bg_align_y" => $bgAlignY,
            ]);
        }

        $this->redirect("/user/" . Session::getUsername());
        return true;

fail:
        $this->doMainPage();
        return true;
    }

    private function doMainPage(): void
    {
        $result = DB::querySingle("SELECT html FROM user_descriptions WHERE user_id=:id", [
            "id" => $this->userId
        ]);
        if ($result != null)
        {
            $this->data->description = $result->html;
        }

        if ($this->isSelf)
        {
            $this->data->editingProfile = (isset($_GET["edit"]) && $_GET["edit"] == 1);
        }
        else
        {
            $this->data->isFollowing = UserUtils::isFollowingUser($this->userId);
        }

        $followerIDs = DB::query("SELECT from_id FROM followers WHERE to_id=:id ORDER BY created_at DESC LIMIT 8", [
            "id" => $this->userId
        ]);
        $this->data->followers = User::fromIds(array_map(fn($i) => $i->from_id, $followerIDs), true);

        $followingIDs = DB::query("SELECT to_id FROM followers WHERE from_id=:id ORDER BY created_at DESC LIMIT 8", [
            "id" => $this->userId
        ]);
        $this->data->following = User::fromIds(array_map(fn($i) => $i->to_id, $followingIDs), true);

        $followerCounts = DB::querySingle("SELECT followers_count, following_count FROM user_follower_counts WHERE user_id=:id", [
            "id" => $this->userId
        ]);

        $this->data->followerCount = $followerCounts->followers_count;
        $this->data->followingCount = $followerCounts->following_count;
    }
}