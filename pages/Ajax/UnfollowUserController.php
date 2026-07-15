<?php
namespace CustoDesk\Page\Ajax;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\DB;
use CustoDesk\Util\TimeUtils;
use CustoDesk\Util\UserUtils;

class UnfollowUserController extends AjaxController
{
    public function onPost(RequestMetadata $request, object $data): bool
    {
        if (!Session::isLoggedIn())
        {
            $this->addAlert(AlertType::ERROR, "Please log in to do that.");
            http_response_code(401);
            return false;
        }

        if (!isset($data->id) || !is_int($data->id))
        {
            return false;
        }

        /* If the user is already not followed, we're all good. */
        if (!UserUtils::isFollowingUser($data->id))
        {
            return true;
        }

        if (!UserUtils::usernameFromId($data->id))
        {
            $this->addAlert(AlertType::ERROR, "That user doesn't exist.");
            return false;
        }

        DB::exec("DELETE FROM followers WHERE from_id=:from_id AND to_id=:to_id", [
            "from_id" => Session::getUserId(),
            "to_id" => $data->id
        ]);

        return true;
    }
}