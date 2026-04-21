@extends('dashboard.layouts.app', [
    'title' => 'Audit Log',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#5E1C3D]">Audit Log</h1>
            <p class="mt-3 text-[18px] text-[#703967]">
                Halaman ini menampilkan histori aktivitas request, perubahan data, dan event penting aplikasi.
            </p>
        </div>

        <article class="rounded-2xl border border-[#efd2ea] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <form method="GET" class="mb-6 grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-2 block text-[14px] font-medium text-[#5E1C3D]">Channel</label>
                    <select name="channel" class="w-full rounded-xl border border-[#e6d8ef] px-4 py-3 text-[14px] outline-none">
                        <option value="">Semua Channel</option>
                        @foreach ($channels as $channel)
                            <option value="{{ $channel }}" @selected(request('channel') === $channel)>{{ ucfirst($channel) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-[14px] font-medium text-[#5E1C3D]">Event</label>
                    <input type="text" name="event" value="{{ request('event') }}"
                        class="w-full rounded-xl border border-[#e6d8ef] px-4 py-3 text-[14px] outline-none"
                        placeholder="Contoh: model.updated">
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit"
                        class="inline-flex rounded-xl bg-[#802A76] px-5 py-3 text-[14px] font-semibold text-white transition hover:bg-[#741f58]">
                        Filter
                    </button>
                    <a href="{{ route('dashboard.audit-logs.index') }}"
                        class="inline-flex rounded-xl border border-[#d7bfd9] px-5 py-3 text-[14px] font-semibold text-[#5E1C3D]">
                        Reset
                    </a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px]">
                    <thead>
                        <tr class="border-b border-[#f1e4f1] text-left text-[13px] uppercase tracking-[0.08em] text-[#8b6b87]">
                            <th class="px-3 py-3">Waktu</th>
                            <th class="px-3 py-3">Channel</th>
                            <th class="px-3 py-3">Event</th>
                            <th class="px-3 py-3">Aktor</th>
                            <th class="px-3 py-3">Subjek</th>
                            <th class="px-3 py-3">Deskripsi</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php
                                $channelClass = match ($log->channel) {
                                    'auth' => 'bg-[#e7f0ff] text-[#1d4ed8]',
                                    'model' => 'bg-[#f3ebff] text-[#7c3aed]',
                                    'business' => 'bg-[#e9fbf1] text-[#15803d]',
                                    default => 'bg-[#fff3df] text-[#b45309]',
                                };
                            @endphp
                            <tr class="border-b border-[#f6eef6] text-[14px] text-[#3b2438]">
                                <td class="px-3 py-4 whitespace-nowrap">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                                <td class="px-3 py-4">
                                    <span class="rounded-full px-3 py-1 text-[12px] font-semibold {{ $channelClass }}">
                                        {{ $log->channel }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 font-medium">{{ $log->event }}</td>
                                <td class="px-3 py-4">{{ $log->actor_name ?? '-' }}</td>
                                <td class="px-3 py-4">{{ $log->subject_label ?? '-' }}</td>
                                <td class="px-3 py-4">{{ $log->description ?? '-' }}</td>
                                <td class="px-3 py-4 whitespace-nowrap">{{ $log->status_code ?? '-' }}</td>
                                <td class="px-3 py-4 text-center">
                                    <a href="{{ route('dashboard.audit-logs.show', ['id' => $log->id]) }}"
                                        class="inline-flex rounded-lg border border-[#dec7df] px-3 py-2 text-[13px] font-semibold text-[#5E1C3D]">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-8 text-center text-[14px] text-[#7c6679]">
                                    Belum ada data audit log.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </article>
    </section>
@endsection
