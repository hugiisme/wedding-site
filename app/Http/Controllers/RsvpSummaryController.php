<?php

namespace App\Http\Controllers;

use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RsvpSummaryController extends Controller
{
    private const ATTENDING_LABELS = [
        'yes' => 'Tham dự',
        'no' => 'Không tham dự',
    ];

    private const CEREMONY_LABELS = [
        'send_bride' => 'Đi đưa dâu',
        'receive_bride' => 'Đi đón dâu',
        'no' => 'Không tham dự lễ gia tiên',
    ];

    public function index(Request $request)
    {
        $query = Rsvp::query();

        // Search theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Filter: tham dự tiệc
        if ($request->filled('attending')) {
            $query->where('attending', $request->input('attending'));
        }

        // Filter: lễ gia tiên
        if ($request->filled('ceremony_attendance')) {
            if ($request->input('ceremony_attendance') === 'none') {
                $query->whereNull('ceremony_attendance');
            } else {
                $query->where('ceremony_attendance', $request->input('ceremony_attendance'));
            }
        }

        // Sort
        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        if (!in_array($sortDir, ['asc', 'desc'])) {
            $sortDir = 'desc';
        }
        $allowedSort = ['name', 'attending', 'ceremony_attendance', 'created_at'];
        if (in_array($sortField, $allowedSort)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $rsvps = $query->paginate($request->input('per_page', 15))
            ->withQueryString();

        // Tổng hợp số lượng (toàn bộ phản hồi, không theo bộ lọc)
        $stats = [
            'total' => Rsvp::count(),
            'attending_yes' => Rsvp::where('attending', 'yes')->count(),
            'attending_no' => Rsvp::where('attending', 'no')->count(),
            'ceremony_send_bride' => Rsvp::where('ceremony_attendance', 'send_bride')->count(),
            'ceremony_receive_bride' => Rsvp::where('ceremony_attendance', 'receive_bride')->count(),
            'ceremony_no' => Rsvp::where('ceremony_attendance', 'no')->count(),
        ];

        return view('rsvp-summary', [
            'rsvps' => $rsvps,
            'stats' => $stats,
            'attendingLabels' => self::ATTENDING_LABELS,
            'ceremonyLabels' => self::CEREMONY_LABELS,
        ]);
    }

    /**
     * Xuất ra file CSV (mở được bằng Excel).
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Rsvp::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }
        if ($request->filled('attending')) {
            $query->where('attending', $request->input('attending'));
        }
        if ($request->filled('ceremony_attendance')) {
            if ($request->input('ceremony_attendance') === 'none') {
                $query->whereNull('ceremony_attendance');
            } else {
                $query->where('ceremony_attendance', $request->input('ceremony_attendance'));
            }
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        $allowedSort = ['name', 'attending', 'ceremony_attendance', 'created_at'];
        if (in_array($sortField, $allowedSort)) {
            $query->orderBy($sortField, $sortDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $rsvps = $query->get();
        $attendingLabels = self::ATTENDING_LABELS;
        $ceremonyLabels = self::CEREMONY_LABELS;

        $filename = 'rsvp-phan-hoi-' . now()->format('Y-m-d-His') . '.csv';

        return Response::streamDownload(function () use ($rsvps, $attendingLabels, $ceremonyLabels) {
            $out = fopen('php://output', 'w');
            // BOM cho Excel đọc UTF-8
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['STT', 'Họ và tên', 'Tham dự tiệc', 'Lễ gia tiên', 'Ngày phản hồi']);
            $stt = 1;
            foreach ($rsvps as $r) {
                fputcsv($out, [
                    $stt++,
                    $r->name,
                    $attendingLabels[$r->attending] ?? $r->attending,
                    $r->ceremony_attendance ? ($ceremonyLabels[$r->ceremony_attendance] ?? $r->ceremony_attendance) : '—',
                    $r->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
