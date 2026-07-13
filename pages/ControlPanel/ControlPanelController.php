<?php
namespace CustoDesk\Page\ControlPanel;

use CustoDesk\DB;
use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\Parser\RichTextProcessor;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\Util\UserUtils;

use function CustoDesk\rootpath;
use Imagick;

class ControlPanelController extends PageController
{
    public string $template = "cpanel/general";
    public string $title = "User Control Panel";
    public string $subpage = "general";

    private function setUp(RequestMetadata $request): void
    {
        if (!Session::isLoggedIn())
        {
            $this->redirectToLogin();
        }

        if (isset($request->path[1]))
        {
            switch ($request->path[1])
            {
                case "description":
                {
                    $this->template = "cpanel/description";
                    $this->subpage = "description";

                    $result = DB::querySingle("SELECT source, editor FROM user_descriptions WHERE user_id=:id", [
                        "id" => Session::getUserId()
                    ]);
                    if ($result != null)
                    {
                        $this->data->rich_edit_source = $result->source;
                        $this->data->rich_edit_choice = $result->editor;
                    }
                    break;
                }
            }
        }

        $this->data->user = User::fromId(Session::getUserId());
    }

    public function onGet(RequestMetadata $request): bool
    {
        $this->setUp($request);
        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        $action = isset($_POST["action"]) ? $_POST["action"] : "";
        switch ($action)
        {
            case "upload_avatar":
            {
                if (isset($_FILES["avatar"]))
                {
                    if ($_FILES["avatar"]["error"] == UPLOAD_ERR_FORM_SIZE
                    || $_FILES["avatar"]["error"] == UPLOAD_ERR_INI_SIZE
                    || $_FILES["avatar"]["size"] > 10485760)
                    {
                        $this->addAlert(AlertType::ERROR, "The uploaded avatar is too big. It must be 10MB at most.");
                        goto done;
                    }
                    else if ($_FILES["avatar"]["error"] != 0)
                    {
                        $this->addAlert(AlertType::ERROR, "There was an error uploading your avatar.");
                        goto done;
                    }

                    $tmpPath = $_FILES["avatar"]["tmp_name"];
                    try
                    {
                        $im = new Imagick($tmpPath);
                        $im2 = $im->coalesceImages();
                        foreach ($im2 as $frame)
                        {
                            $im = $frame;
                            break;
                        }
                    }
                    catch (\Throwable $e)
                    {
                        $this->addAlert(AlertType::ERROR, "The uploaded avatar is not an image.");
                        goto done;
                    }


                    $im->resizeImage(64, 64, Imagick::FILTER_LANCZOS, 0);
                    $fname = md5(Session::getUserId() . "_" . md5($im, true));

                    if ($im->writeImage(rootpath("user_avatars/$fname.png")))
                    {
                        // Remove old pfp and set new one
                        $result = DB::querySingle("SELECT fname FROM user_avatars WHERE user_id=:id", [
                            "id" => Session::getUserId()
                        ]);
                        if ($result != null)
                        {
                            if ($result->fname != $fname)
                            {
                                unlink(rootpath("user_avatars/{$result->fname}.png"));

                                DB::exec("UPDATE user_avatars SET fname=:fname WHERE user_id=:id", [
                                    "fname" => $fname,
                                    "id" => Session::getUserId()
                                ]);
                            }
                        }
                        else
                        {
                            DB::exec("INSERT INTO user_avatars (user_id, fname) VALUES (:id, :fname)", [
                                "id" => Session::getUserId(),
                                "fname" => $fname
                            ]);
                        }
                    }
                    else
                    {
                        $this->addAlert(AlertType::ERROR, "Failed to write the uploaded avatar.");
                        goto done;
                    }
                }
                break;   
            }
            case "remove_avatar":
                UserUtils::removeAvatar(Session::getUserId());
                break;
            case "edit_description":
            {
                $text = RichTextProcessor::processRichText($this->data);
                $length = strlen($text->source);
                if ($length > 10000)
                {
                    $this->addAlert(AlertType::ERROR, "Your description contains $length characters. It must be 10000 characters at most.");
                    // Return early instead of setting up. The description from the DB is all we set up
                    // on this page and we'd rather keep the user input.
                    return true;
                }

                $result = DB::querySingle("SELECT user_id FROM user_descriptions WHERE user_id=:id", [
                    "id" => Session::getUserId()
                ]);
                if ($result == null)
                {
                    DB::exec("INSERT INTO user_descriptions (user_id, html, source, editor) VALUES (:id, :html, :source, :editor)", [
                        "html" => $text->html,
                        "source" => $text->source,
                        "editor" => $text->editor,
                        "id" => Session::getUserId()
                    ]);
                }
                else
                {
                    DB::exec("UPDATE user_descriptions SET html=:html, source=:source, editor=:editor WHERE user_id=:id", [
                        "html" => $text->html,
                        "source" => $text->source,
                        "editor" => $text->editor,
                        "id" => Session::getUserId()
                    ]);
                }
                break;
            }
        }

done:
        $this->setUp($request);
        return true;
    }
}