<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
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
    // --- TAMBAHKAN LOG DISINI ---
        ActivityLog::create([
            'user_id'     => auth()->id() ?? 1,
            'activity'    => 'create',
            'description' => 'Membuat event baru: ' . $event->title,
            'ip_address'  => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Event berhasil dibuat',
            'data' => $event
        ], 201);
}
public function update(Request $request, $id)
{
    // Cari data event berdasarkan ID
    $event = Event::find($id);

    if (!$event) {
        return response()->json(['message' => 'Event tidak ditemukan'], 404);
    }

    // Update datanya
    $event->update($request->all());

    return response()->json([
        'message' => 'Event berhasil diupdate',
        'data' => $event
    ], 200);
}
public function destroy($id)
    {
        // 1. Cari data event berdasarkan ID
        $event = Event::find($id);

        // 2. Jika data tidak ditemukan, beri respon error
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Data Event Tidak Ditemukan!'
            ], 404);
        }

        // Simpan nama event sebentar untuk catatan di log
        $namaEvent = $event->title;

        // 3. Hapus data dari tabel events
        $event->delete();

        // 4. CATAT KE ACTIVITY LOG
        ActivityLog::create([
            'user_id'     => auth()->id() ?? 1, // Jika belum login, otomatis pakai ID 1
            'activity'    => 'delete',
            'description' => 'Berhasil menghapus event: ' . $namaEvent,
            'ip_address'  => request()->ip(),
        ]);

        // 5. Beri respon sukses
        return response()->json([
            'success' => true,
            'message' => 'Event Berhasil Dihapus dan Log Tercatat!'
        ], 200);
    }
}


