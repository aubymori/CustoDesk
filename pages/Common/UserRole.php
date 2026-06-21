<?php
namespace CustoDesk\Page\Common;

enum UserRole : string
{
    case MEMBER = "Member";
    case MOD = "Moderator";
    case ADMIN = "Administrator";

    /* Roles are hierarchical and stacking, so this makes the most sense for comparison. */
    private function toOrdinal(): int
    {
        return match ($this)
        {
            self::MEMBER => 0,
            self::MOD    => 1,
            self::ADMIN  => 2,
        };
    }

    public function isAtLeast(UserRole $other): bool
    {
        return $this->toOrdinal() >= $other->toOrdinal();
    }

    public function isAbove(UserRole $other): bool
    {
        return $this->toOrdinal() > $other->toOrdinal();
    }

    public function isBelow(UserRole $other): bool
    {
        return $this->toOrdinal() < $other->toOrdinal();
    }
}