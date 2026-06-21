<?php
namespace CustoDesk\TemplateUtils;

use CustoDesk\Page\Common\UserRole;
use CustoDesk\Util\UserUtils;

class UserUtilsDelegate
{
    public function idFromUsername(string $username): int
    {
        return UserUtils::idFromUsername($username);
    }

    public function usernameFromId(int $id): string
    {
        return UserUtils::usernameFromId($id);
    }

    public function getRole(int $id): UserRole
    {
        return UserUtils::getRole($id);
    }
}