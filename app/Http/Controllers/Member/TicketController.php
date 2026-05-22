<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\TicketMember;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = TicketMember::where('id_user', auth()->id())
            ->orderBy('update_at', 'desc')
            ->get();
        return view('member.tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $content = [
            [
                'sender' => 'user',
                'name' => auth()->user()->full_name ?? auth()->user()->username ?? 'User',
                'message' => $request->message,
                'created_at' => now()->toDateTimeString(),
            ]
        ];

        TicketMember::create([
            'id_user' => auth()->id(),
            'subject' => $request->subject,
            'content' => $content,
            'status' => 'Pending',
        ]);

        return back()->with('success', 'Ticket created successfully.');
    }

    public function show($id)
    {
        $ticket = TicketMember::where('id_user', auth()->id())->findOrFail($id);
        return view('member.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = TicketMember::where('id_user', auth()->id())->findOrFail($id);

        if ($ticket->status === 'Closed') {
            return back()->withErrors(['error' => 'Cannot reply to a closed ticket.']);
        }

        $content = $ticket->content ?? [];
        $content[] = [
            'sender' => 'user',
            'name' => auth()->user()->full_name ?? auth()->user()->username ?? 'User',
            'message' => $request->message,
            'created_at' => now()->toDateTimeString(),
        ];

        $ticket->update([
            'content' => $content,
            'status' => 'Reply',
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }
}
