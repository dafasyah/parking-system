<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\ParkingTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $vehicleType = $request->input('vehicle_type');
        $status = $request->input('status');
        $sort = $request->input('sort', 'created_at');
        $dir = $request->input('dir', 'desc');
        
        $query = $this->applyFilters(ParkingTransaction::query(), $from, $to, $vehicleType, $status);
        $stats = $this->calculateStats($from, $to, $vehicleType, $status);
        
        $transactions = $query->with(['entryUser', 'exitUser'])
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->appends($request->only(['from', 'to', 'vehicle_type', 'status', 'sort', 'dir']));
        
        $completedCount = ParkingTransaction::where('status', 'completed')->count();
        
        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats,
                'completedCount' => $completedCount,
                'transactions' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'total' => $transactions->total(),
                ],
            ]);
        }
        
        return view('dashboard.index', compact('stats', 'transactions', 'completedCount'));
    }

    public function charts(Request $request): JsonResponse
    {
        try {
            $from = $request->input('from');
            $to = $request->input('to');
            $vehicleType = $request->input('vehicle_type');
            $status = $request->input('status');
            
            // Revenue chart - last 7 days or custom range
            $last7 = $this->getRevenueChartData($from, $to);
            
            // Vehicle split pie chart (with vehicle type filter)
            $vehicleSplit = $this->getVehicleSplit($from, $to, $vehicleType, $status);
            
            // Hourly bar chart
            $perHour = $this->getHourlyData($from, $to, $vehicleType, $status);
            
            return response()->json([
                'last7' => $last7,
                'vehicleSplit' => $vehicleSplit,
                'perHour' => $perHour,
            ]);
        } catch (\Exception $e) {
            Log::error('Dashboard charts error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data chart'], 500);
        }
    }
    
    /**
     * Apply filters to query
     */
    private function applyFilters($query, $from, $to, $vehicleType, $status)
    {
        if ($from && $to) {
            $query->whereBetween('entry_time', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }
        
        if ($vehicleType) {
            $query->where('vehicle_type', $vehicleType);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query;
    }
    
    /**
     * Calculate dashboard statistics
     */
    private function calculateStats($from, $to, $vehicleType, $status)
    {
        $query = ParkingTransaction::query();
        

        if ($from && $to) {
            $query->whereBetween('entry_time', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }
        

        if ($vehicleType) {
            $query->where('vehicle_type', $vehicleType);
        }
        

        if ($status) {
            $query->where('status', $status);
        }
        
        $parkedCount = (clone $query)->where('status', 'parked')->count();
        
        // Revenue calculations
        $revenueQuery = clone $query;
        $revenueToday = (clone $revenueQuery)
            ->whereDate('exit_time', now()->toDateString())
            ->where('status', 'completed')
            ->sum('parking_fee');
            
        $revenueMonth = (clone $revenueQuery)
            ->whereMonth('exit_time', now()->month)
            ->whereYear('exit_time', now()->year)
            ->where('status', 'completed')
            ->sum('parking_fee');
        
        return [
            'parked' => $parkedCount,
            'revenue_today' => $revenueToday,
            'revenue_month' => $revenueMonth,
        ];
    }
    
    /**
     * Get revenue chart data
     */
    private function getRevenueChartData($from, $to)
    {
        if ($from && $to) {
            $start = Carbon::parse($from);
            $end = Carbon::parse($to);
            $days = $start->diffInDays($end) + 1;
            
            return collect(range(0, min($days - 1, 30)))->map(function($i) use ($start) {
                $day = $start->copy()->addDays($i)->toDateString();
                return [
                    'date' => Carbon::parse($day)->format('d M'),
                    'revenue' => ParkingTransaction::whereDate('exit_time', $day)
                        ->where('status', 'completed')
                        ->sum('parking_fee'),
                ];
            });
        }
        
        // Default: last 7 days
        return collect(range(0, 6))->map(function($i){
            $day = now()->subDays(6-$i)->toDateString();
            return [
                'date' => Carbon::parse($day)->format('d M'),
                'revenue' => ParkingTransaction::whereDate('exit_time', $day)
                    ->where('status', 'completed')
                    ->sum('parking_fee'),
            ];
        });
    }
    
    /**
     * Get vehicle split data
     */
    private function getVehicleSplit($from, $to, $vehicleType, $status)
    {
        $query = ParkingTransaction::query();
        
        if ($from && $to) {
            $query->whereBetween('entry_time', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        }
        
        if ($vehicleType) {
            $query->where('vehicle_type', $vehicleType);
        }
        
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', 'parked');
        }
        
        return $query->selectRaw('vehicle_type, count(*) as total')
            ->groupBy('vehicle_type')
            ->pluck('total', 'vehicle_type');
    }
    
    /**
     * Get hourly transaction data
     */
    private function getHourlyData($from, $to, $vehicleType, $status)
    {
        $query = ParkingTransaction::query();
        
        if ($from && $to) {
            $query->whereBetween('entry_time', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        } else {
            $query->whereDate('entry_time', now()->toDateString());
        }
        
        if ($vehicleType) {
            $query->where('vehicle_type', $vehicleType);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        return $query->selectRaw('HOUR(entry_time) as hour, count(*) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }
}
