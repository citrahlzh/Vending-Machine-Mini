@extends('games.layouts.app', [
    'title' => 'Quiz Game',
])

@section('content')
    <div class="min-h-[calc(100vh-64px)] bg-[#f3f3f6] px-6 py-12 sm:px-10 lg:px-[72px] lg:py-16">
        <div>
            <p>
                Jawab pertanyaan dengan benar dan dapatkan hadiah!
            </p>
        </div>
        <div>
            <p>
                Siapa Presiden pertama Indonesia?
            </p>
        </div>
        <div>
            <ul>
                <li>
                    A. Soekarno
                </li>
                <li>
                    B. Prabowo
                </li>
                <li>
                    C. Jokowi
                </li>
                <li>
                    D. Kaesang
                </li>
            </ul>
        </div>
    </div>
@endsection
