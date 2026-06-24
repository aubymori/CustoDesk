<?php
namespace CustoDesk\Page\Common;

enum UserRole : int
{
    case MEMBER = 0;
    case MOD    = 1;
    case ADMIN  = 2;

    public function isAtLeast(UserRole $other): bool
    {
        return $this->value >= $other->value;
    }

    public function isAbove(UserRole $other): bool
    {
        return $this->value > $other->value;
    }

    public function isBelow(UserRole $other): bool
    {
        return $this->value < $other->value;
    }

    public function toString(): string
    {
        return match ($this)
        {
            self::MEMBER => "Member",
            self::MOD    => "Moderator",
            self::ADMIN  => "Administrator",
        };
    }
}