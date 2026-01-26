<?php

namespace App\Enums;

enum BookFlagsEnum: string
{
    case Hot = 'hot';

    case Best = 'best';
    case New = 'new';
    case Trending = 'trending';
    case Featured = 'featured';
    case Recommended = 'recommended';
}
