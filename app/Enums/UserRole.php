<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN_1 = 'admin_1';
    case ADMIN_2 = 'admin_2';
}
