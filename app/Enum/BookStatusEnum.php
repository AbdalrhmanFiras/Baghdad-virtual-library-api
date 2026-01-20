<?php

namespace App\Enum;

enum BookStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

}
