<?php
namespace CustoDesk\Page\Common;

enum UserRole : string
{
    case MEMBER = "Member";
    case MOD = "Moderator";
    case ADMIN = "Administrator";
}