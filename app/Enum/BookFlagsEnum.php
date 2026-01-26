<?php

namespace App\Enums;

enum BookFlagsEnum: string
{
    case Hot = 'hot';
    // hi try server
    case Best = 'best';
    case New = 'new';
    case Trending = 'trending';
    case Featured = 'featured';
    case Recommended = 'recommended';
}
