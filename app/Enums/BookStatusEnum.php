<?php

namespace App\Enums;

enum BookStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

}
