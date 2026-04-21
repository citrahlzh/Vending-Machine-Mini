@extends('dashboard.layouts.app', [
    'title' => 'Detail Audit Log',
])

@section('content')
    <section class="space-y-6 p-2">
        <div class="flex items-center gap-2">
            <a href="{{ route('dashboard.audit-logs.index') }}">
                <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
            </a>
            <div>
                <h1 class="text-[28px] font-semibold leading-none text-[#5E1C3D]">Detail Audit Log</h1>
                <p class="mt-3 text-[18px] text-[#703967]">
                    Detail lengkap aktivitas yang tercatat pada aplikasi.
                </p>
            </div>
        </div>

        <article class="rounded-2xl border border-[#efd2ea] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-[#f0e2ef] bg-[#fffafb] p-5">
                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">Ringkasan</h2>
                    <dl class="mt-4 space-y-3 text-[14px] text-[#4b2e48]">
                        <div><dt class="font-semibold">Waktu</dt><dd>{{ $log->created_at?->format('d/m/Y H:i:s') }}</dd></div>
                        <div><dt class="font-semibold">Channel</dt><dd>{{ $log->channel }}</dd></div>
                        <div><dt class="font-semibold">Event</dt><dd>{{ $log->event }}</dd></div>
                        <div><dt class="font-semibold">Action</dt><dd>{{ $log->action ?? '-' }}</dd></div>
                        <div><dt class="font-semibold">Deskripsi</dt><dd>{{ $log->description ?? '-' }}</dd></div>
                        <div><dt class="font-semibold">Status Code</dt><dd>{{ $log->status_code ?? '-' }}</dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-[#f0e2ef] bg-[#fffafb] p-5">
                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">Konteks</h2>
                    <dl class="mt-4 space-y-3 text-[14px] text-[#4b2e48]">
                        <div><dt class="font-semibold">Aktor</dt><dd>{{ $log->actor_name ?? '-' }}</dd></div>
                        <div><dt class="font-semibold">Subjek</dt><dd>{{ $log->subject_label ?? '-' }}</dd></div>
                        <div><dt class="font-semibold">Route</dt><dd>{{ $log->route_name ?? '-' }}</dd></div>
                        <div><dt class="font-semibold">Method</dt><dd>{{ $log->method ?? '-' }}</dd></div>
                        <div><dt class="font-semibold">URL</dt><dd class="break-all">{{ $log->url ?? '-' }}</dd></div>
                        <div><dt class="font-semibold">IP</dt><dd>{{ $log->ip_address ?? '-' }}</dd></div>
                    </dl>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-[#f0e2ef] bg-[#fffafb] p-5">
                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">Tags</h2>
                    <pre class="mt-4 overflow-x-auto rounded-xl bg-[#2a1120] p-4 text-[13px] text-[#ffe7f6]">{{ json_encode($log->tags ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
                <div class="rounded-2xl border border-[#f0e2ef] bg-[#fffafb] p-5">
                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">Properties</h2>
                    <pre class="mt-4 overflow-x-auto rounded-xl bg-[#2a1120] p-4 text-[13px] text-[#ffe7f6]">{{ json_encode($log->properties ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </div>
        </article>
    </section>
@endsection
