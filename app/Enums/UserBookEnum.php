<?php

namespace App\Enums;

enum UserBookEnum: string
{
    case None = 'none';
    case Reading = 'reading';
    case Completed = 'completed';
}
