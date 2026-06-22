<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Chatbot AI</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">

    <!-- Header Sederhana Khusus Chatbot -->
    <header class="bg-white shadow">
        <div class="flex items-center justify-between max-w-4xl px-4 py-4 mx-auto sm:px-6 lg:px-8">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <span>🤖</span> Chatbot AI
            </h2>
            <nav>
                @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-indigo-600 hover:underline">Ke
                    Dashboard Admin &rarr;</a>
                @endauth
            </nav>
        </div>
    </header>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white overflow-hidden shadow-xl sm:rounded-2xl flex flex-col h-[75vh] border border-gray-100">

                <!-- CHAT BODY + HISTORY -->
                <div class="flex-1 p-6 space-y-6 overflow-y-auto bg-gray-50" id="chatBody">

                    <!-- Pesan Pembuka AI -->
                    <div class="flex justify-start">
                        <div
                            class="bg-white border border-gray-200 text-gray-800 max-w-[85%] md:max-w-[75%] p-4 rounded-2xl rounded-tl-none shadow-sm">
                            <p class="text-[15px] leading-relaxed">Halo! Saya adalah asisten AI Anda. Ada yang bisa saya
                                bantu hari ini?</p>
                        </div>
                    </div>

                    @foreach($chats as $chat)
                    <!-- User Message -->
                    <div class="flex justify-end">
                        <div
                            class="bg-indigo-600 text-white max-w-[85%] md:max-w-[75%] p-4 rounded-2xl rounded-tr-none shadow-sm">
                            <p class="text-[15px] leading-relaxed">{{ $chat->message }}</p>
                            <span
                                class="text-[10px] text-indigo-200 mt-1 block text-right">{{ $chat->created_at->format('H:i') }}</span>
                        </div>
                    </div>

                    <!-- Bot Message -->
                    <div class="flex justify-start">
                        <div
                            class="bg-white border border-gray-200 text-gray-800 max-w-[85%] md:max-w-[75%] p-4 rounded-2xl rounded-tl-none shadow-sm">
                            <p class="text-[15px] leading-relaxed">{!! nl2br(e($chat->reply)) !!}</p>
                            <span
                                class="text-[10px] text-gray-400 mt-1 block">{{ $chat->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                    @endforeach

                </div>

                <!-- CHAT FOOTER (INPUT) -->
                <div class="p-4 bg-white border-t border-gray-100">
                    <div class="relative flex items-center gap-3">
                        <input type="text" id="message" autocomplete="off"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-full px-6 py-3.5 focus:outline-none focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 text-[15px] transition-all"
                            placeholder="Ketik pesan Anda di sini..."
                            onkeypress="if(event.key==='Enter') sendMessage()">

                        <button onclick="sendMessage()"
                            class="flex items-center justify-center w-12 h-12 text-white transition-transform bg-indigo-600 rounded-full shadow-md hover:bg-indigo-700 shrink-0 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    function sendMessage() {
        let messageInput = document.getElementById('message');
        let message = messageInput.value;

        if (!message.trim()) return;

        let chatBody = document.getElementById('chatBody');
        let time = new Date().toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit'
        });

        // Tampilkan pesan user ke layar
        chatBody.insertAdjacentHTML('beforeend', `
            <div class="flex justify-end">
                <div class="bg-indigo-600 text-white max-w-[85%] md:max-w-[75%] p-4 rounded-2xl rounded-tr-none shadow-sm">
                    <p class="text-[15px] leading-relaxed">${message.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>
                    <span class="text-[10px] text-indigo-200 mt-1 block text-right">${time}</span>
                </div>
            </div>
        `);

        messageInput.value = "";

        // Tampilkan animasi "loading" dari bot
        let loadingId = 'loading-' + Date.now();
        chatBody.insertAdjacentHTML('beforeend', `
            <div class="flex justify-start" id="${loadingId}">
                <div class="bg-white border border-gray-200 text-gray-500 max-w-[85%] md:max-w-[75%] p-4 rounded-2xl rounded-tl-none shadow-sm flex gap-1 items-center">
                    <span class="animate-bounce">.</span><span class="animate-bounce" style="animation-delay: 0.2s">.</span><span class="animate-bounce" style="animation-delay: 0.4s">.</span>
                </div>
            </div>
        `);

        scrollToBottom();

        // Panggil endpoint backend chatbot
        fetch("{{ route('chat.send') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() } }'
            },
            body: JSON.stringify({
                message: message
            })
        })
    .then(res => res.json())
        .then(data => {
            document.getElementById(loadingId).remove();

            let reply = data.answer || "Maaf, tidak ada respon dari sistem.";
            let formattedReply = reply.replace(/\n/g, '<br>');

            // Tampilkan jawaban
            chatBody.insertAdjacentHTML('beforeend', `
                <div class="flex justify-start">
                    <div class="bg-white border border-gray-200 text-gray-800 max-w-[85%] md:max-w-[75%] p-4 rounded-2xl rounded-tl-none shadow-sm">
                        <p class="text-[15px] leading-relaxed">${formattedReply}</p>
                        <span class="text-[10px] text-gray-400 mt-1 block">${time}</span>
                    </div>
                </div>
            `);
            scrollToBottom();
        })
        .catch(error => {
            console.error(error);
            document.getElementById(loadingId).remove();
        });
    }

    // Otomatis gulir ke paling bawah saat halaman di-load
    window.onload = function() {
        scrollToBottom();
    };

    function scrollToBottom() {
        let chatBody = document.getElementById('chatBody');
        chatBody.scrollTop = chatBody.scrollHeight;
    }
    </script>
</body>

</html>
