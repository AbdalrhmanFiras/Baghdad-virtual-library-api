<?php

namespace App\Enum;


Enum BookStatusEnum:string{

    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

}