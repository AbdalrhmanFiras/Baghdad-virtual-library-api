<?php

namespace App\Enums;

enum TicketsStatusEnum: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';
}
