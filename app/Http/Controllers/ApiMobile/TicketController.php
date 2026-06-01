<?php

namespace App\Http\Controllers\ApiMobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TicketMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Helper to authenticate mobile client
     */
    private function authUser(Request $request)
    {
        $apiKey = $request->input('api_key') ?: $request->input('key');
        
        if (!$apiKey) {
            $authHeader = $request->header('Authorization');
            if ($authHeader) {
                if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                    $apiKey = $matches[1];
                } else {
                    $apiKey = $authHeader;
                }
            }
        }

        if (!$apiKey) {
            return null;
        }

        return User::where('api_key', $apiKey)->where('status', 'Active')->first();
    }

    /**
     * Retrieve paginated support tickets for the mobile user.
     */
    public function index(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $limit = $request->input('limit', 10);

        $tickets = TicketMember::where('id_user', $user->id)
            ->orderBy('update_at', 'desc')
            ->paginate($limit);

        $formattedTickets = collect($tickets->items())->map(function ($ticket) {
            return [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->create_at ? $ticket->create_at->toDateTimeString() : null,
                'updated_at' => $ticket->update_at ? $ticket->update_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedTickets,
            'pagination' => [
                'total' => $tickets->total(),
                'per_page' => $tickets->perPage(),
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'from' => $tickets->firstItem(),
                'to' => $tickets->lastItem()
            ]
        ]);
    }

    /**
     * Create a new support ticket.
     */
    public function store(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $content = [
            [
                'sender' => 'user',
                'name' => $user->full_name ?: $user->username ?: 'User',
                'message' => $request->message,
                'created_at' => now()->toDateTimeString(),
            ]
        ];

        $ticket = TicketMember::create([
            'id_user' => $user->id,
            'subject' => $request->subject,
            'content' => $content,
            'status' => 'Pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket created successfully.',
            'data' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->create_at ? $ticket->create_at->toDateTimeString() : null,
                'updated_at' => $ticket->update_at ? $ticket->update_at->toDateTimeString() : null,
            ]
        ], 201);
    }

    /**
     * View details of a single ticket and its replies.
     */
    public function show(Request $request, $id)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $ticket = TicketMember::where('id_user', $user->id)->find($id);
        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->create_at ? $ticket->create_at->toDateTimeString() : null,
                'updated_at' => $ticket->update_at ? $ticket->update_at->toDateTimeString() : null,
                'replies' => $ticket->content ?? [],
            ]
        ]);
    }

    /**
     * Reply to a ticket.
     */
    public function reply(Request $request, $id)
    {
        $user = $this->authUser($request);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid, inactive, or missing API key.'
            ], 401);
        }

        $ticket = TicketMember::where('id_user', $user->id)->find($id);
        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($ticket->status === 'Closed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot reply to a closed ticket.'
            ], 400);
        }

        $content = $ticket->content ?? [];
        $content[] = [
            'sender' => 'user',
            'name' => $user->full_name ?: $user->username ?: 'User',
            'message' => $request->message,
            'created_at' => now()->toDateTimeString(),
        ];

        $ticket->update([
            'content' => $content,
            'status' => 'Reply',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Reply sent successfully.',
            'data' => [
                'id' => $ticket->id,
                'status' => $ticket->status,
                'updated_at' => $ticket->update_at ? $ticket->update_at->toDateTimeString() : null,
                'replies' => $content
            ]
        ]);
    }
}
