@extends('dashboard.layouts.app', [
    'title' => 'Dashboard'
])

@section('content')
    <section class="space-y-6">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Dashboard</h1>
            <p class="mt-2 text-[18px] text-[#4F3970]">Halaman ini untuk menampilkan ringkasan data yang diperlukan.</p>
        </div>

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1fr)_300px]">
            <div class="space-y-4">
                <div class="grid gap-4 lg:grid-cols-3">
                    <article
                        class="rounded-3xl bg-[#5A2F7E] px-8 py-6 text-white">
                        <div class="flex items-center gap-5">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#9B79CC]/75 text-4xl">
                                <img src="{{ asset('assets/icons/dashboard/omset.svg') }}" alt="" class="w-[35px]">
                            </div>
                            <div>
                                <p class="text-[15px] font-semibold">Omzet Hari Ini</p>
                                <p class="mt-1 text-[18px] font-semibold leading-none text-[#E5E0EC]">
                                    Rp {{ number_format($omzetHariIni, 0, ',', '.') }},-
                                </p>
                            </div>
                        </div>
                    </article>

                    <article
                        class="rounded-3xl bg-[#5A2F7E] px-8 py-6 text-white">
                        <div class="flex items-center gap-5">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#9B79CC]/75 text-4xl">
                                <img src="{{ asset('assets/icons/dashboard/sukses.svg') }}" alt="" class="w-[35px]">
                            </div>
                            <div>
                                <p class="text-[15px] font-semibold">Transaksi Sukses</p>
                                <p class="mt-1 text-[18px] font-semibold leading-none text-[#E5E0EC]">{{ $transaksiSukses }}
                                </p>
                            </div>
                        </div>
                    </article>

                    <article
                        class="rounded-3xl bg-[#5A2F7E] px-8 py-6 text-white">
                        <div class="flex items-center gap-5">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#9B79CC]/75 text-4xl">
                                <img src="{{ asset('assets/icons/dashboard/gagal.svg') }}" alt="" class="w-[35px]">
                            </div>
                            <div>
                                <p class="text-[15px] font-semibold">Transaksi Gagal</p>
                                <p class="mt-1 text-[18px] font-semibold leading-none text-[#E5E0EC]">{{ $transaksiGagal }}
                                </p>
                            </div>
                        </div>
                    </article>
                </div>

                <article class="rounded-3xl border border-[#ddd2ef] bg-white px-10 py-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                    <h2 class="text-center text-[18px] font-semibold text-[#3C1C5E]">Transaksi Hari ini</h2>

                    <div class="mt-7 rounded-xl border border-transparent bg-white p-4">
                        <div class="h-[180px] w-full">
                            <canvas id="transactionsChart"></canvas>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="rounded-3xl border border-[#ddd2ef] bg-white px-10 py-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-center text-[20px] font-semibold text-[#3C1C5E]">Aksi Cepat</h2>
                <ul class="mt-8 space-y-5 text-[15px] font-semibold text-[#3C1C5E]">
                    <li>
                        <a href="{{ route('dashboard.transactions.index') }}" class="inline-flex items-center gap-3 hover:text-[#2d1248]">
                            Lihat Daftar Transaksi
                            <i class='bx bx-up-arrow-alt rotate-45 text-lg'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.products.index') }}" class="inline-flex items-center gap-3 hover:text-[#2d1248]">
                            Lihat Daftar Produk
                            <i class='bx bx-up-arrow-alt rotate-45 text-lg'></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard.product-displays.index') }}"
                            class="inline-flex items-center gap-3 hover:text-[#2d1248]">
                            Isi Kembali Stok Produk
                            <i class='bx bx-up-arrow-alt rotate-45 text-lg'></i>
                        </a>
                    </li>
                </ul>
            </aside>
        </div>
    </section>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            const el = document.getElementById('transactionsChart');
            if (!el) return;

            const labels = @json($chartLabels);
            const values = @json($chartValues);
            const maxValue = Math.max(...values, 0);
            const yAxisMax = Math.max(5, Math.ceil(maxValue / 5) * 5);

            new Chart(el, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: '#5A3A82',
                        borderRadius: 3,
                        barThickness: 22
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#3C1C5E',
                            titleFont: {
                                family: 'Poppins'
                            },
                            bodyFont: {
                                family: 'Poppins'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#9282AD',
                                font: {
                                    family: 'Poppins',
                                    size: 13
                                }
                            },
                            border: {
                                color: '#4A296C',
                                width: 2
                            }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMax: yAxisMax,
                            ticks: {
                                stepSize: 5,
                                color: '#9282AD',
                                font: {
                                    family: 'Poppins',
                                    size: 13
                                }
                            },
                            grid: {
                                display: false
                            },
                            border: {
                                color: '#4A296C',
                                width: 2
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
