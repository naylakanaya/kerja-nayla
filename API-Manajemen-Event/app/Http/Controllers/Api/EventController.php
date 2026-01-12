<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;


class EventController extends Controller
{
    // GET /api/events
    public function index()
{
    $events = Event::with(['category', 'creator'])->get();

    return response()->json([
        'message' => 'List event',
        'data' => $events
    ]);
}



    // POST /api/events
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string',
        'event_date' => 'required|date',
        'quota' => 'required|integer|min:1',
        'location' => 'required|string',
        'category_id' => 'required|exists:categories,id',
    ]);

    $event = Event::create([
        'title' => $validated['title'],
        'event_date' => $validated['event_date'],
        'quota' => $validated['quota'],
        'location' => $validated['location'],
        'category_id' => $validated['category_id'],
        'created_by' => auth()->id(), // 🔥 INI KUNCI UTAMANYA
    ]);

    return response()->json([
        'message' => 'Event berhasil dibuat',
        'data' => $event
    ], 201);
}

}