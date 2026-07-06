<?php
namespace CustoDesk\Page\Admin;

use CustoDesk\RequestMetadata;
use CustoDesk\DB;
use CustoDesk\Page\Common\AlertType;
use CustoDesk\Parser\RichTextProcessor;
use CustoDesk\Session;
use CustoDesk\Util\TimeUtils;

class AlertsController extends AdminPageController
{
    public string $template = "admin/alerts";
    public string $subpage = "alerts";
    public string $title = "Alerts";

    private function getAlerts(): void
    {
        $this->data->dbAlerts = DB::query("SELECT * from alerts") ?? [];
    }

    public function onGet(RequestMetadata $request): bool
    {
        if (isset($_GET["delete"]))
        {
            $id = $_GET["delete"];
            $result = DB::querySingle("SELECT id FROM alerts WHERE id=:id", [
                "id" => $id
            ]);
            if ($result != null)
            {
                DB::exec("DELETE FROM alerts WHERE id=:id", [
                    "id" => $id
                ]);
            }
        }

        $this->getAlerts();
        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        $text = RichTextProcessor::processRichText();
        if ($text === false)
        {
            $this->addAlert(AlertType::ERROR, "The alert must have text.");
            goto fail;
        }

        $type = AlertType::NORMAL;
        if (isset($_POST["type"]))
        {
            $type = AlertType::tryFrom($_POST["type"]) ?? AlertType::NORMAL;
        }

        DB::exec("INSERT INTO alerts (text, type, creator_id, created_at) VALUES (:text, :type, :creator_id, :created_at)", [
            "text" => $text->html,
            "type" => $type->value,
            "creator_id" => Session::getUserId(),
            "created_at" => TimeUtils::now()
        ]);

fail:
        $this->getAlerts();
        return true;
    }
}