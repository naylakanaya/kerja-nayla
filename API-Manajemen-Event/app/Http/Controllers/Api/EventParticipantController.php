<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\Event;
use Illuminate\Http\Request;

class EventParticipantController extends Controller
{
    public function join($id)
{
    $user = auth()->user();
    $event = Event::findOrFail($id);

    if ($event->users()->where('user_id', $user->id)->exists()) {
        return response()->json([
            'message' => 'Anda sudah terdaftar di event ini'
        ], 409);
    }

    $event->users()->attach($user->id);

    return response()->json([
        'message' => 'Berhasil join event'
    ], 201);
}


    public function participants($id)
    {
        $event = Event::with('users')->find($id);

        if (!$event) {
            return ApiResponse::error('Event tidak ditemukan', 404);
        }

        return ApiResponse::success($event->users, 'Daftar peserta');
    }


}
