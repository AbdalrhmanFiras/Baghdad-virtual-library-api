<?php

namespace App\Enum;

enum UserBookEnum: string
{
    case None = 'none';
    case Reading = 'reading';
    case Completed = 'completed';
}
