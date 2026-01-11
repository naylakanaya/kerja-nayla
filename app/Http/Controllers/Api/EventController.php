<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

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
        'created_by' => auth()->id(),
    ]);

    // 🔥 ACTIVITY LOG
    ActivityLogger::log(
        'create_event',
        'Membuat event: ' . $event->title
    );

    return response()->json([
        'message' => 'Event berhasil dibuat',
        'data' => $event
    ], 201);
}

    public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $validated = $request->validate([
        'title' => 'required|string',
        'event_date' => 'required|date',
        'quota' => 'required|integer|min:1',
        'location' => 'required|string',
        'category_id' => 'required|exists:categories,id',
    ]);

    $event->update($validated);

    // 🔥 ACTIVITY LOG UPDATE
    ActivityLogger::log(
        action: 'update_event',
        description: 'Update event: ' . $event->title
    );

    return response()->json([
        'message' => 'Event berhasil diupdate',
        'data' => $event
    ]);
}

    // DELETE /api/events/{id}
    public function destroy($id)
{
    $event = Event::findOrFail($id);

    // 🔒 hanya pembuat event yg boleh hapus
    if ($event->created_by !== auth()->id()) {
        return response()->json([
            'message' => 'Tidak punya akses menghapus event ini'
        ], 403);
    }

    $title = $event->title;
    $event->delete();

    // 📝 Activity Log
    ActivityLogger::log(
        action: 'delete_event',
        description: 'Menghapus event: ' . $title
    );

    return response()->json([
        'message' => 'Event berhasil dihapus'
    ]);
}

}
