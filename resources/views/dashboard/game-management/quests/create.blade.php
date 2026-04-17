@extends('dashboard.layouts.app', [
    'title' => 'Tambah Soal',
])

@section('content')
    <section class="space-y-6 p-2">

        <div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.quests.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>

                <h1 class="text-[28px] font-semibold text-[#5E1C3D]">
                    Tambah Soal
                </h1>

            </div>

            <p class="mt-2 text-[#703967]">
                Tambahkan soal ke bank soal permainan.
            </p>

        </div>


        <article class="rounded-[26px] border border-[#efd2ea] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <form id="createQuestForm" class="space-y-4">

                {{-- GAME TYPE --}}
                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
                        Tipe Game
                    </label>

                    <select name="game_type" id="gameType"
                        class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                        <option value="">Pilih tipe game</option>
                        <option value="quiz">Quiz</option>
                        <option value="guess_image">Tebak Gambar</option>

                    </select>

                </div>


                {{-- QUESTION TYPE --}}
                <div id="questionTypeWrapper" class="hidden">

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
                        Tipe Soal
                    </label>

                    <select name="type" id="questionType"
                        class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                        <option value="multiple_choice">Pilihan Ganda</option>
                        <option value="text">Jawaban Teks</option>

                    </select>

                </div>


                {{-- PROMPT --}}
                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
                        Pertanyaan
                    </label>

                    <textarea name="prompt"
                        class="w-full rounded-lg border border-[#d896c4] px-3 py-2 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]"
                        rows="3" placeholder="Masukkan pertanyaan"></textarea>

                </div>


                {{-- IMAGE --}}
                <div id="imageWrapper" class="hidden">

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
                        Gambar
                    </label>

                    <input type="file" name="image_url"
                        class="block w-full text-sm text-[#5E1C3D] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#802A76] file:text-white hover:file:bg-[#741f58] border border-[#d896c4] rounded-lg cursor-pointer">

                </div>


                {{-- OPTIONS --}}
                <div id="optionWrapper" class="hidden space-y-3">

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
                        Pilihan Jawaban
                    </label>

                    <input type="hidden" id="correctAnswerInput" name="correct_answer" disabled>

                    <div id="optionList" class="space-y-3"></div>

                    <button type="button" id="addOptionButton"
                        class="h-10 rounded-lg border border-[#802A76] bg-white px-4 text-[14px] font-semibold text-[#741f58] transition hover:bg-[#f8f4ff]">
                        Tambah Pilihan
                    </button>

                </div>


                {{-- ANSWER --}}
                <div id="textAnswerWrapper" class="hidden">

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
                        Jawaban Benar
                    </label>

                    <input type="text" id="textAnswerInput" name="correct_answer"
                        class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                </div>


                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.game-management.quests.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#802A76] bg-white text-[15px] font-semibold text-[#741f58] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>

                    <button type="submit"
                        class="h-10 rounded-lg bg-[#802A76] text-[15px] font-semibold text-white transition hover:bg-[#741f58]">
                        Simpan Soal
                    </button>
                </div>

            </form>

        </article>

    </section>
@endsection

@push('script')
    <script>
        const gameType = document.getElementById('gameType')
        const questionType = document.getElementById('questionType')

        const questionTypeWrapper = document.getElementById('questionTypeWrapper')
        const optionWrapper = document.getElementById('optionWrapper')
        const optionList = document.getElementById('optionList')
        const addOptionButton = document.getElementById('addOptionButton')
        const correctAnswerInput = document.getElementById('correctAnswerInput')
        const textAnswerWrapper = document.getElementById('textAnswerWrapper')
        const textAnswerInput = document.getElementById('textAnswerInput')
        const imageWrapper = document.getElementById('imageWrapper')
        let optionCounter = 0
        let correctKey = null


        /*
        GAME TYPE
        */

        gameType.addEventListener('change', function() {

            const value = this.value

            // tampilkan pilihan tipe soal
            questionTypeWrapper.classList.remove('hidden')

            if (value === 'guess_image') {

                imageWrapper.classList.remove('hidden')

            } else {

                imageWrapper.classList.add('hidden')

            }

            syncQuestionType()

        })


        /*
        QUESTION TYPE
        */

        function syncQuestionType() {
            const type = questionType.value

            if (type === 'multiple_choice') {

                optionWrapper.classList.remove('hidden')
                textAnswerWrapper.classList.add('hidden')
                textAnswerInput.disabled = true
                correctAnswerInput.disabled = false
                ensureDefaultOptions()

            } else {

                optionWrapper.classList.add('hidden')
                textAnswerWrapper.classList.remove('hidden')
                textAnswerInput.disabled = false
                correctAnswerInput.disabled = true

            }
        }

        questionType.addEventListener('change', syncQuestionType)

        function ensureDefaultOptions() {
            if (optionList.children.length > 0) {
                return
            }

            addOption()
            addOption()
            addOption()
            addOption()
        }

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
                    'h-10 w-[92px] rounded-lg bg-[#5E1C3D] text-[14px] font-semibold text-white transition' :
                    'h-10 w-[92px] rounded-lg border border-[#d896c4] bg-white text-[14px] font-semibold text-[#5E1C3D] transition hover:bg-[#f8f4ff]'
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
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#5E1C3D] text-[14px] font-semibold text-white">
                    ${key}.
                </div>
                <input type="text" name="option[${key}]" value="${text.replace(/"/g, '&quot;')}"
                    placeholder="Jawaban pilihan ${key}"
                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                <button type="button" data-correct-button data-key="${key}">Salah</button>
                <button type="button" data-remove-button data-key="${key}"
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EAE3F5] text-[16px] font-semibold text-[#933e77] transition hover:bg-[#e0d7ee]">
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

        addOptionButton.addEventListener('click', () => {
            addOption()
        })


        /*
        SUBMIT API
        */

        document
            .getElementById('createQuestForm')
            .addEventListener('submit', async function(e) {
                e.preventDefault()
                const formData = new FormData(this)

                try {
                    const response = await fetch('/api/quest/store', {
                        method: 'POST',
                        body: formData
                    })

                    const data = await response.json()

                    if (!response.ok) {
                        throw new Error(data.message || 'Gagal menyimpan soal')
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
