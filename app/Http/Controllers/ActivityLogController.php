<?php
namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $query = Activity::with('causer');
            
            // Search by description
            if ($request->filled('search')) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }
            
            // Filter by user
            if ($request->filled('user_id')) {
                $query->where('causer_id', $request->user_id)
                      ->where('causer_type', User::class);
            }
            
            // Filter by log_name (activity type)
            if ($request->filled('log_name')) {
                $query->where('log_name', $request->log_name);
            }
            
            // Filter by date range
            if ($request->filled('from') && $request->filled('to')) {
                $query->whereBetween('created_at', [
                    \Carbon\Carbon::parse($request->from)->startOfDay(),
                    \Carbon\Carbon::parse($request->to)->endOfDay()
                ]);
            }
            

            $activities = $query->latest()
                ->paginate(30)
                ->appends($request->only(['search', 'user_id', 'log_name', 'from', 'to']));
            

            $users = User::orderBy('name')->get();
            

            $logNames = Activity::distinct()->pluck('log_name')->filter();
            
            activity()
                ->causedBy(auth()->user())
                ->withProperties(['ip' => $request->ip()])
                ->log('Mengakses halaman activity log');
            
            return view('activity.index', compact('activities', 'users', 'logNames'));
        } catch (\Exception $e) {
            Log::error('Activity log error: ' . $e->getMessage());
            return view('activity.index')->with('error', 'Gagal memuat activity log');
        }
    }
}
