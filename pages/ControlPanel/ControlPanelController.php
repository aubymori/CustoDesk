<?php
namespace CustoDesk\Page\ControlPanel;

use CustoDesk\DB;
use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\Parser\RichTextProcessor;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

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
                    break;
                }
            }
        }

        $this->data->user = User::fromId(Session::getUserId());
    }

    public function onGet(RequestMetadata $request): bool
    {
        $this->setUp($request);

        if (isset($request->path[1]))
        {
            switch ($request->path[1])
            {
                case "description":
                {
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

        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        $this->setUp($request);

        $action = isset($_POST["action"]) ? $_POST["action"] : "";
        switch ($action)
        {
            case "edit_description":
            {
                $text = RichTextProcessor::processRichText($this->data);
                $length = strlen($text->source);
                if ($length > 10000)
                {
                    $this->addAlert(AlertType::ERROR, "Your description contains $length characters. It must be 10000 characters at most.");
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

        return true;
    }
}