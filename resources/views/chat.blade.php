@extends('layouts.public')

@section('content')
<div class="container mx-auto py-6 max-w-3xl">
    <h1 class="text-2xl font-bold mb-4">Чат</h1>

    <!-- Список сообщений -->
    <div id="messages" class="space-y-2 border p-3 rounded max-h-[70vh] overflow-y-auto">
        @foreach ($messages as $message)
            <div class="message" data-id="{{ $message->id }}" data-original="{{ $message->content }}"
            @if($message->translated_content)
                data-translated="{{ $message->translated_content }}"
            @endif
            >
                <strong>{{ $message->user->name }}</strong>:
                <div class="content-wrapper">
                    <div class="content">
                        {{ $message->translated_content ?? $message->content }}
                    </div>
                    @if ($message->translated_content)
                        <div class="original-content text-sm text-gray-500 hidden">
                            {{ $message->content }}
                        </div>
                    @endif
                </div>
                @if ($message->translated_content)
                    <a href="#" class="toggle-original text-blue-600 text-sm">[+]</a>
                @endif
            </div>
        @endforeach
    </div>

    <form id="messageForm">
        <input type="text" name="content" id="messageInput" required class="w-full border rounded p-2" rows="2" placeholder="Введите сообщение...">
        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded">Отправить</button>
    </form>


    <!-- Авторизация / Регистрация -->
    <div class="mb-4">
        @auth
            <span>Вы вошли как <strong>{{ auth()->user()->name }}</strong></span> |
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit">Выйти</button>
            </form>
        @else
            <a href="{{ route('login') }}">Войти</a> |
            <a href="{{ route('register') }}">Регистрация</a>
        @endauth
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('messageForm');
    if (!form) return; // защита

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const input = document.getElementById('messageInput');
        const content = input.value;

        fetch('/messages', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ content })
        })
        .then(response => response.json())
        .then(() => {
            input.value = '';
        });
    });

    if (typeof window.Echo !== 'undefined') {
        const currentUserLang = document.documentElement.lang;

        console.log('Echo создаётся…');

        window.Echo.connector.socket.on('connect', () => {
            console.log('✅ WebSocket подключено!');
        });

        window.Echo.connector.socket.on('connect_error', (error) => {
            console.error('❌ Ошибка подключения WebSocket:', error);
        });

        window.Echo.connector.socket.on('reconnect_attempt', () => {
            console.log('♻️ Попытка переподключения...');
        });

        window.Echo.channel('chat')
            .subscribed(() => {
                console.log('✅ Подписка на канал chat выполнена');
            })
            .listen('.message.sent', (e) => {
                console.log('📥 Получено событие message.sent:', e);
                const data = e.message;
                const messagesDiv = document.getElementById('messages');

                const messageEl = document.createElement('div');
                messageEl.classList.add('message');
                messageEl.dataset.id = data.id;
                messageEl.dataset.original = data.content;

                const contentWrapper = document.createElement('div');
                contentWrapper.classList.add('content-wrapper');

                const contentEl = document.createElement('div');
                contentEl.classList.add('content');

                // Найдём перевод
                const translation = data.translations?.find(
                    t => t.language === currentUserLang
                );
                const isTranslated = translation && translation.translated_content !== data.content;

                contentEl.textContent = isTranslated ? translation.translated_content : data.content;
                contentWrapper.appendChild(contentEl);

                if (isTranslated) {
                    const originalEl = document.createElement('div');
                    originalEl.classList.add('original-content', 'text-sm', 'text-gray-500', 'hidden');
                    originalEl.textContent = data.content;
                    contentWrapper.appendChild(originalEl);
                }

                messageEl.innerHTML = `<strong>${data.user.name}</strong>:`; // автор
                messageEl.appendChild(contentWrapper);

                if (isTranslated) {
                    const toggleLink = document.createElement('a');
                    toggleLink.href = '#';
                    toggleLink.classList.add('toggle-original', 'text-blue-600', 'text-sm');
                    toggleLink.textContent = '[+]';

                    messageEl.appendChild(toggleLink);
                }

                messagesDiv.appendChild(messageEl);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            });
    } else {
        console.error('Echo is not available.');
    }
});


    // Обработчик [+] / [-] — общий для всех сообщений
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('toggle-original')) {
            e.preventDefault();

            const messageEl = e.target.closest('.message');
            const originalEl = messageEl.querySelector('.original-content');

            if (originalEl.classList.contains('hidden')) {
                originalEl.classList.remove('hidden');
                e.target.textContent = '[-]';
            } else {
                originalEl.classList.add('hidden');
                e.target.textContent = '[+]';
            }
        }
    });
</script>
@endpush
