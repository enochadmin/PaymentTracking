<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionController extends Controller
{
    /**
     * Display a listing of active sessions.
     */
    public function index()
    {
        $sessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select('sessions.*', 'users.name', 'users.email')
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) {
                // Convert UNIX timestamp to Carbon instance
                $session->last_activity_human = Carbon::createFromTimestamp($session->last_activity)->diffForHumans();
                $session->last_activity_date = Carbon::createFromTimestamp($session->last_activity);
                return $session;
            });

        return view('sessions.index', compact('sessions'));
    }
}
