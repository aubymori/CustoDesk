<?php
namespace CustoDesk\Page\Common;

use CustoDesk\DB;
use CustoDesk\TemplateUtils\VFL;
use CustoDesk\Util\UserUtils;

class User
{
    public string $username;
    public int $id;
    public string $avatarUrl;
    public int $createdAt;
    public UserRole $role;

    public static function fromId(int $id): self
    {
        $new = new self;
        $new->id = $id;

        $result = DB::querySingle("SELECT username, created_at, role FROM users WHERE id=:id", [
            "id" => $id
        ]);
        if ($result)
        {
            $new->username = $result->username;
            $new->createdAt = $result->created_at;
            $new->role = UserRole::from($result->role);
        }

        $new->avatarUrl = VFL::getInstance()->resolveImage("userIcon");

        return $new;
    }

    public static function fromUsername(string $username): self
    {
        return self::fromId(UserUtils::idFromUsername($username));
    }
}