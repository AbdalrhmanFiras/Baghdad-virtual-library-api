<?php

namespace App\Enums;

enum TicketTypeEnum: string
{
    case BUG = 'bug';
    case FEATURE_REQUEST = 'feature_request';
    case SUPPORT = 'support';
    case IMPROVEMENT = 'improvement';
    case OTHER = 'other';
}
