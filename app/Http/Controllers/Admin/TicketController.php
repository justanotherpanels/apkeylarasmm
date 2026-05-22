<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketMember;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = TicketMember::with('user')
            ->orderBy('update_at', 'desc')
            ->get();
        
        return view('admin.ticket.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = TicketMember::with('user')->findOrFail($id);
        return view('admin.ticket.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = TicketMember::findOrFail($id);

        if ($ticket->status === 'Closed') {
            return back()->withErrors(['error' => 'Cannot reply to a closed ticket.']);
        }

        $content = $ticket->content ?? [];
        $content[] = [
            'sender' => 'admin',
            'name' => auth()->user()->full_name ?? auth()->user()->username ?? 'Support Admin',
            'message' => $request->message,
            'created_at' => now()->toDateTimeString(),
        ];

        $ticket->update([
            'content' => $content,
            'status' => 'Response', // Status changed to Response when admin replies
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }

    public function close($id)
    {
        $ticket = TicketMember::findOrFail($id);
        $ticket->update(['status' => 'Closed']);
        return back()->with('success', 'Ticket closed successfully.');
    }
}
