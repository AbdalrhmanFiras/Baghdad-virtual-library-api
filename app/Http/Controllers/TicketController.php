<?php

namespace App\Http\Controllers;

use App\Enums\TicketsStatusEnum;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Tickets Endpoint
 */
class TicketController extends Controller
{
    /**
     * Get All Tickets
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())->get();

        return $tickets ? $this->responseSuccess(TicketResource::collection($tickets)) : $this->responseError(null, 'No tickets found', 404);
    }

    /**
     * Create a new Ticket
     */
    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated()
            + ['user_id' => Auth::id()];
        $ticket = Ticket::create($data);

        $ticket->status = TicketsStatusEnum::OPEN->value;

        return $ticket ? $this->responseSuccess('Ticket created successfully', 201) : $this->responseError(null, 'Failed to create ticket');
    }

    /**
     * Get a specific Ticket
     */
    public function show($id)
    {
        $ticket = Ticket::where('id', $id)->where('user_id', Auth::id())->first();

        return $ticket ? $this->responseSuccess(new TicketResource($ticket)) : $this->responseError(null, 'Ticket not found', 404);
    }
}
