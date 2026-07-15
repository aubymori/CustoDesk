<?php
namespace CustoDesk\Page\Profile;

use CustoDesk\DB;
use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\Util\UserUtils;

class ProfileController extends PageController
{
    public string $template = "profile/main";
    private int $userId;
    private bool $isSelf;

    public function onGet(RequestMetadata $request): bool
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

        if (!$this->isSelf)
        {
            $this->data->isFollowing = UserUtils::isFollowingUser($this->userId);
        }
    }
}