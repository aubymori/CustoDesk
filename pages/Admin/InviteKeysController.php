<?php
namespace CustoDesk\Page\Admin;

use CustoDesk\DB;
use CustoDesk\RequestMetadata;
use CustoDesk\ServerConfig;
use CustoDesk\Session;
use CustoDesk\Util\TimeUtils;

class InviteKeysController extends AdminPageController
{
    public string $template = "admin/invite_keys";
    public string $subpage = "invite_keys";
    public string $title = "Invite Keys";

    private function getKeys(): void
    {
        $this->data->keys = DB::query("SELECT * FROM invite_keys");
    }

    public function onGet(RequestMetadata $request): bool
    {
        if (!ServerConfig::requireInviteKeys())
        {
            $this->data->disabled = true;
            return true;
        }

        if (isset($_GET["revoke"]))
        {
            $key = $_GET["revoke"];
            $result = DB::querySingle("SELECT user_id FROM invite_keys WHERE key=:key", [
                "key" => $key
            ]);
            // Only allow revokation of invite keys that exist and are not tied to an
            // existing user.
            if ($result != null && $result->user_id == null)
            {
                DB::exec("DELETE FROM invite_keys WHERE key=:key", [
                    "key" => $key
                ]);
            }
        }

        $this->getKeys();
        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        if (!ServerConfig::requireInviteKeys())
        {
            $this->data->disabled = true;
            return true;
        }

        if ($_POST["generate"] == "1")
        {
            do
            {
                $newKey = str_pad(dechex(rand(0, 0xFFFFFFFF)), 8, "0", STR_PAD_LEFT);
                $result = DB::querySingle("SELECT key FROM invite_keys WHERE key=:key", [
                    "key" => $newKey
                ]);
            }
            while ($result != null);

            DB::exec("INSERT INTO invite_keys (key, creator_id, created_at) VALUES (:key, :creator_id, :created_at)", [
                "key" => $newKey,
                "creator_id" => Session::getUserId(),
                "created_at" => TimeUtils::now()
            ]);
        }

        $this->getKeys();
        return true;
    }
}