<?php
namespace CustoDesk\Page\Ajax;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\DB;
use CustoDesk\Util\TimeUtils;
use CustoDesk\Util\UserUtils;

class FollowUserController extends AjaxController
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

        if ($data->id == Session::getUserId())
        {
            $this->addAlert(AlertType::ERROR, "You can't follow yourself.");
            return false;
        }

        /* If the user is already followed, we're all good. */
        if (UserUtils::isFollowingUser($data->id))
        {
            return true;
        }

        if (!UserUtils::usernameFromId($data->id))
        {
            $this->addAlert(AlertType::ERROR, "That user doesn't exist.");
            return false;
        }

        DB::exec("INSERT INTO followers (from_id, to_id, created_at) VALUES (:from_id, :to_id, :created_at)", [
            "from_id" => Session::getUserId(),
            "to_id" => $data->id,
            "created_at" => TimeUtils::now()
        ]);

        UserUtils::updateFollowerCounts($data->id, updateFollowers: true);
        UserUtils::updateFollowerCounts(Session::getUserId(), updateFollowing: true);

        return true;
    }
}