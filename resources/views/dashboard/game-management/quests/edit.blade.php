@extends('dashboard.layouts.app', [
    'title' => 'Edit Soal',
])

@section('content')

    <section class="space-y-6 p-2">

        <div>
            <div class="flex items-center gap-2">

                <a href="{{ route('dashboard.game-management.quests.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>

                <h1 class="text-[28px] font-semibold text-[#3C1C5E]">
                    Edit Soal
                </h1>

            </div>

            <p class="mt-2 text-[#4F3970]">
                Perbarui soal permainan.
            </p>
        </div>


        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <form id="updateQuestForm" class="space-y-4">


                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Tipe Game</label>

                    <select name="game_type"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                        <option value="quiz" {{ $quest->game_type == 'quiz' ? 'selected' : '' }}>
                            Quiz
                        </option>

                        <option value="guess_image" {{ $quest->game_type == 'guess_image' ? 'selected' : '' }}>
                            Tebak Gambar
                        </option>

                    </select>

                </div>


                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Tipe Soal</label>

                    <select name="type"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                        <option value="multiple_choice" {{ $quest->type == 'multiple_choice' ? 'selected' : '' }}>
                            Pilihan Ganda
                        </option>

                        <option value="text" {{ $quest->type == 'text' ? 'selected' : '' }}>
                            Jawaban Teks
                        </option>

                    </select>

                </div>


                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Pertanyaan</label>

                    <textarea name="prompt"
                        class="w-full rounded-lg border border-[#B596D8] px-3 py-2 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]"
                        rows="3">{{ $quest->prompt }}</textarea>

                </div>


                <div id="optionWrapper" class="space-y-3">

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                        Pilihan Jawaban
                    </label>

                    <input type="hidden" id="correctAnswerInput" name="correct_answer" value="{{ $quest->answer['correct_answer'] ?? '' }}">

                    <div id="optionList" class="space-y-3"></div>

                    <button type="button" id="addOptionButton"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white px-4 text-[14px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Tambah Pilihan
                    </button>

                </div>


                <div id="textAnswerWrapper">

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Jawaban Benar</label>

                    <input type="text" id="textAnswerInput" name="correct_answer"
                        value="{{ $quest->answer['correct_answer'] }}"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                </div>


                @if ($quest->game_type === 'guess_image')
                    <div>

                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Gambar</label>

                        <input type="file" name="image_url"
                            class="block w-full text-sm text-[#3C1C5E] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#5A2F7E] file:text-white hover:file:bg-[#4B1F74] border border-[#B596D8] rounded-lg cursor-pointer">

                        @if ($quest->image_url)
                            <img src="{{ asset('storage/' . $quest->image_url) }}" class="mt-2 h-[120px]">
                        @endif

                    </div>
                @endif


                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.game-management.quests.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>

                    <button type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Update Soal
                    </button>
                </div>


            </form>

        </article>

    </section>

@endsection

@push('script')
    <script>
        const questionTypeSelect = document.querySelector('select[name="type"]')
        const optionWrapper = document.getElementById('optionWrapper')
        const optionList = document.getElementById('optionList')
        const addOptionButton = document.getElementById('addOptionButton')
        const correctAnswerInput = document.getElementById('correctAnswerInput')
        const textAnswerWrapper = document.getElementById('textAnswerWrapper')
        const textAnswerInput = document.getElementById('textAnswerInput')
        const initialOptions = @json($quest->option ?? []);
        let optionCounter = 0
        let correctKey = null

        function nextOptionKey(index) {
            return String.fromCharCode(65 + index)
        }

        function updateCorrectAnswer() {
            correctAnswerInput.value = correctKey || ''
        }

        function setCorrectKey(key) {
            correctKey = key
            updateCorrectAnswer()
            updateButtons()
        }

        function updateButtons() {
            optionList.querySelectorAll('[data-correct-button]').forEach((button) => {
                const key = button.getAttribute('data-key')
                const isCorrect = key === correctKey
                button.textContent = isCorrect ? 'Benar' : 'Salah'
                button.className = isCorrect ?
                    'h-10 w-[92px] rounded-lg bg-[#3C1C5E] text-[14px] font-semibold text-white transition' :
                    'h-10 w-[92px] rounded-lg border border-[#B596D8] bg-white text-[14px] font-semibold text-[#3C1C5E] transition hover:bg-[#f8f4ff]'
            })
        }

        function addOption(text = '', keyOverride = null) {
            const key = keyOverride || nextOptionKey(optionCounter)
            const keyIndex = key.charCodeAt(0) - 65
            optionCounter = Math.max(optionCounter, keyIndex + 1)

            const row = document.createElement('div')
            row.className = 'flex items-center gap-3'
            row.setAttribute('data-option-row', key)

            row.innerHTML = `
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#3C1C5E] text-[14px] font-semibold text-white">
                    ${key}.
                </div>
                <input type="text" name="option[${key}]" value="${text.replace(/"/g, '&quot;')}"
                    placeholder="Jawaban pilihan ${key}"
                    class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                <button type="button" data-correct-button data-key="${key}">Salah</button>
                <button type="button" data-remove-button data-key="${key}"
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EAE3F5] text-[16px] font-semibold text-[#6B3E93] transition hover:bg-[#e0d7ee]">
                    ✕
                </button>
            `

            optionList.appendChild(row)
            const input = row.querySelector(`input[name="option[${key}]"]`)
            input.addEventListener('input', () => {
                if (key === correctKey) {
                    updateCorrectAnswer()
                }
            })

            row.querySelector('[data-correct-button]').addEventListener('click', () => {
                setCorrectKey(key)
            })

            row.querySelector('[data-remove-button]').addEventListener('click', () => {
                removeOption(key)
            })

            if (!correctKey) {
                setCorrectKey(key)
            } else {
                updateButtons()
            }
        }

        function removeOption(key) {
            const rows = optionList.querySelectorAll('[data-option-row]')
            if (rows.length <= 2) {
                return
            }

            const row = optionList.querySelector(`[data-option-row="${key}"]`)
            if (row) {
                row.remove()
            }

            if (correctKey === key) {
                const firstRow = optionList.querySelector('[data-option-row]')
                correctKey = firstRow ? firstRow.getAttribute('data-option-row') : null
            }

            updateCorrectAnswer()
            updateButtons()
        }

        function syncQuestionType() {
            const type = questionTypeSelect.value
            if (type === 'multiple_choice') {
                optionWrapper.classList.remove('hidden')
                textAnswerWrapper.classList.add('hidden')
                textAnswerInput.disabled = true
                correctAnswerInput.disabled = false
            } else {
                optionWrapper.classList.add('hidden')
                textAnswerWrapper.classList.remove('hidden')
                textAnswerInput.disabled = false
                correctAnswerInput.disabled = true
            }
        }

        if (Array.isArray(initialOptions) && initialOptions.length) {
            initialOptions.forEach((opt) => {
                const key = opt.key || nextOptionKey(optionCounter)
                addOption(opt.text || '', key)
            })
        } else {
            addOption()
            addOption()
        }

        if (correctAnswerInput.value) {
            setCorrectKey(correctAnswerInput.value)
        } else if (!correctKey && optionList.children.length) {
            const firstRow = optionList.querySelector('[data-option-row]')
            if (firstRow) {
                setCorrectKey(firstRow.getAttribute('data-option-row'))
            }
        }

        addOptionButton.addEventListener('click', () => {
            addOption()
        })

        questionTypeSelect.addEventListener('change', syncQuestionType)
        syncQuestionType()

        document.getElementById('updateQuestForm')
            .addEventListener('submit', async function(e) {

                e.preventDefault()

                const formData = new FormData(this)

                try {

                    const response = await fetch('/api/quest/update/{{ $quest->id }}', {
                        method: 'POST',
                        body: formData
                    })

                    const data = await response.json()

                    if (!response.ok) {
                        throw new Error(data.message)
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message
                    })

                    setTimeout(() => {
                        window.location.href =
                            "{{ route('dashboard.game-management.quests.index') }}";
                    }, 1400);

                } catch (err) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: err.message
                    })

                }

            })
    </script>
@endpush
