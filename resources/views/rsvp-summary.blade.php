<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tổng hợp phản hồi RSVP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'wedding-brown': '#4a3f35',
                        'wedding-sage': '#ced0ab',
                        'wedding-gold-warm': '#c9a962',
                        'wedding-cream': '#f2f1eb',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-wedding-cream text-wedding-brown min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-2xl md:text-3xl font-semibold text-wedding-brown border-b border-wedding-sage pb-2 mb-6">
            Tổng hợp & thống kê phản hồi RSVP
        </h1>

        {{-- Tổng hợp số lượng --}}
        <div class="mb-6 rounded-xl border border-wedding-sage/50 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-wedding-brown/70 mb-3">Tổng hợp số lượng</h2>
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                <span class="text-wedding-brown/90"><strong class="text-wedding-brown">{{ $stats['total'] }}</strong> phản hồi</span>
                <span class="text-wedding-brown/80">·</span>
                <span><strong>{{ $stats['attending_yes'] }}</strong> {{ $attendingLabels['yes'] }}</span>
                <span><strong>{{ $stats['attending_no'] }}</strong> {{ $attendingLabels['no'] }}</span>
                <span class="text-wedding-brown/80">·</span>
                <span><strong>{{ $stats['ceremony_send_bride'] }}</strong> {{ $ceremonyLabels['send_bride'] }}</span>
                <span><strong>{{ $stats['ceremony_receive_bride'] }}</strong> {{ $ceremonyLabels['receive_bride'] }}</span>
                <span><strong>{{ $stats['ceremony_no'] }}</strong> {{ $ceremonyLabels['no'] }}</span>
            </div>
        </div>

        {{-- Filter + Search --}}
        <form method="get" action="{{ route('rsvp.summary') }}" class="mb-6 space-y-4">
            <div class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="block text-sm font-medium text-wedding-brown/80 mb-1">Tìm theo tên</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Họ tên..."
                           class="w-full rounded-lg border border-wedding-sage/60 bg-white px-3 py-2 text-wedding-brown focus:border-wedding-gold-warm focus:ring-1 focus:ring-wedding-gold-warm">
                </div>
                <div>
                    <label for="attending" class="block text-sm font-medium text-wedding-brown/80 mb-1">Tham dự tiệc</label>
                    <select name="attending" id="attending" class="rounded-lg border border-wedding-sage/60 bg-white px-3 py-2 text-wedding-brown focus:border-wedding-gold-warm">
                        <option value="">— Tất cả —</option>
                        @foreach($attendingLabels as $val => $label)
                            <option value="{{ $val }}" {{ request('attending') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="ceremony_attendance" class="block text-sm font-medium text-wedding-brown/80 mb-1">Lễ gia tiên</label>
                    <select name="ceremony_attendance" id="ceremony_attendance" class="rounded-lg border border-wedding-sage/60 bg-white px-3 py-2 text-wedding-brown focus:border-wedding-gold-warm">
                        <option value="">— Tất cả —</option>
                        <option value="none" {{ request('ceremony_attendance') === 'none' ? 'selected' : '' }}>Không có / Không tham dự</option>
                        @foreach($ceremonyLabels as $val => $label)
                            <option value="{{ $val }}" {{ request('ceremony_attendance') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="sort" value="{{ request('sort', 'created_at') }}">
                <input type="hidden" name="dir" value="{{ request('dir', 'desc') }}">
                <input type="hidden" name="per_page" value="{{ request('per_page', 15) }}">
                <button type="submit" class="rounded-lg bg-wedding-gold-warm px-4 py-2 text-white font-medium hover:opacity-90">
                    Lọc
                </button>
            </div>
        </form>

        {{-- Export --}}
        @php
            $exportUrl = route('rsvp.export', array_filter([
                'search' => request('search'),
                'attending' => request('attending'),
                'ceremony_attendance' => request('ceremony_attendance'),
                'sort' => request('sort', 'created_at'),
                'dir' => request('dir', 'desc'),
            ]));
        @endphp
        <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ $exportUrl }}" class="inline-flex items-center rounded-lg bg-wedding-sage px-4 py-2 text-wedding-brown font-medium hover:bg-wedding-sage/80">
                Xuất Excel (CSV)
            </a>
            <div class="flex items-center gap-2">
                <span class="text-sm text-wedding-brown/70">Hiển thị</span>
                <form method="get" action="{{ route('rsvp.summary') }}" class="inline" id="per-page-form">
                    @foreach(request()->except('per_page', 'page') as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <select name="per_page" onchange="this.form.submit()" class="rounded border border-wedding-sage/60 bg-white px-2 py-1 text-sm">
                        @foreach([10, 15, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ request('per_page', 15) == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </form>
                <span class="text-sm text-wedding-brown/70">/ trang</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-xl border border-wedding-sage/50 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-wedding-sage/40">
                <thead class="bg-wedding-sage/30">
                    <tr>
                        @php
                            $sort = request('sort', 'created_at');
                            $dir = request('dir', 'desc');
                            $nextDir = $dir === 'asc' ? 'desc' : 'asc';
                            $url = fn($field) => request()->fullUrlWithQuery(['sort' => $field, 'dir' => ($sort === $field ? $nextDir : 'desc'), 'page' => null]);
                        @endphp
                        <th class="px-4 py-3 text-left text-xs font-semibold text-wedding-brown uppercase tracking-wider">STT</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-wedding-brown uppercase tracking-wider">
                            <a href="{{ $url('name') }}" class="hover:text-wedding-gold-warm">Họ và tên {{ $sort === 'name' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-wedding-brown uppercase tracking-wider">
                            <a href="{{ $url('attending') }}" class="hover:text-wedding-gold-warm">Tham dự tiệc {{ $sort === 'attending' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-wedding-brown uppercase tracking-wider">
                            <a href="{{ $url('ceremony_attendance') }}" class="hover:text-wedding-gold-warm">Lễ gia tiên {{ $sort === 'ceremony_attendance' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-wedding-brown uppercase tracking-wider">
                            <a href="{{ $url('created_at') }}" class="hover:text-wedding-gold-warm">Ngày phản hồi {{ $sort === 'created_at' ? ($dir === 'asc' ? '↑' : '↓') : '' }}</a>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-wedding-sage/30">
                    @forelse($rsvps as $index => $r)
                        <tr class="hover:bg-wedding-sage/10">
                            <td class="px-4 py-3 text-sm text-wedding-brown/90">{{ $rsvps->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-wedding-brown">{{ $r->name }}</td>
                            <td class="px-4 py-3 text-sm text-wedding-brown/90">{{ $attendingLabels[$r->attending] ?? $r->attending }}</td>
                            <td class="px-4 py-3 text-sm text-wedding-brown/90">
                                @if($r->ceremony_attendance)
                                    {{ $ceremonyLabels[$r->ceremony_attendance] ?? $r->ceremony_attendance }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-wedding-brown/80">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-wedding-brown/60">Chưa có phản hồi nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($rsvps->hasPages())
            <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-wedding-brown/70">
                    Hiển thị {{ $rsvps->firstItem() }}–{{ $rsvps->lastItem() }} / {{ $rsvps->total() }} phản hồi
                </p>
                <nav class="flex gap-1">
                    @if($rsvps->onFirstPage())
                        <span class="rounded-lg border border-wedding-sage/40 px-3 py-2 text-sm text-wedding-brown/50 cursor-not-allowed">Trước</span>
                    @else
                        <a href="{{ $rsvps->previousPageUrl() }}" class="rounded-lg border border-wedding-sage/60 bg-white px-3 py-2 text-sm text-wedding-brown hover:bg-wedding-sage/20">Trước</a>
                    @endif
                    @foreach($rsvps->getUrlRange(max(1, $rsvps->currentPage() - 2), min($rsvps->lastPage(), $rsvps->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}" class="rounded-lg border px-3 py-2 text-sm {{ $page === $rsvps->currentPage() ? 'border-wedding-gold-warm bg-wedding-gold-warm text-white' : 'border-wedding-sage/60 bg-white text-wedding-brown hover:bg-wedding-sage/20' }}">{{ $page }}</a>
                    @endforeach
                    @if($rsvps->hasMorePages())
                        <a href="{{ $rsvps->nextPageUrl() }}" class="rounded-lg border border-wedding-sage/60 bg-white px-3 py-2 text-sm text-wedding-brown hover:bg-wedding-sage/20">Sau</a>
                    @else
                        <span class="rounded-lg border border-wedding-sage/40 px-3 py-2 text-sm text-wedding-brown/50 cursor-not-allowed">Sau</span>
                    @endif
                </nav>
            </div>
        @endif
    </div>
</body>
</html>
