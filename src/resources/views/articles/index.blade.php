<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenn記事まとめ</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 text-gray-800">

    <header class="bg-[#3ea8ff] px-10 py-5 sticky top-0 z-50">
        <h1 class="text-white text-xl font-bold">Zenn記事まとめ</h1>
    </header>

    <div class="max-w-275 mx-auto my-8 px-4">

        <div class="flex gap-3 mb-6">
            <a href="?topic=laravel"
               class="px-5 py-2 rounded-full font-bold text-sm {{ $topic === 'laravel' ? 'bg-[#3ea8ff] text-white' : 'bg-white text-gray-600 hover:bg-gray-200' }}">
                Laravel
            </a>
            <a href="?topic=nextjs"
               class="px-5 py-2 rounded-full font-bold text-sm {{ $topic === 'nextjs' ? 'bg-[#3ea8ff] text-white' : 'bg-white text-gray-600 hover:bg-gray-200' }}">
                Next.js
            </a>
        </div>

        <div class="flex flex-col md:flex-row gap-6 items-start">

            <main class="flex-1 min-w-0">
                @forelse ($articles as $article)
                    <div class="bg-white rounded-lg p-5 mb-4 shadow-sm flex gap-4 items-start">
                        <div class="text-4xl shrink-0">{{ $article->emoji ?? '📝' }}</div>
                        <div class="flex-1">
                            <a href="{{ $article->url }}" target="_blank" rel="noopener"
                               class="font-bold text-gray-900 hover:text-[#3ea8ff] no-underline">
                                {{ $article->title }}
                            </a>
                            <div class="mt-2 text-sm text-gray-400 flex gap-4">
                                <span>{{ $article->author_name }}</span>
                                <span>{{ $article->published_at->format('Y/m/d') }}</span>
                                <span class="text-red-400">♥ {{ $article->liked_count }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>記事がありません。<code>php artisan zenn:fetch {{ $topic }}</code> を実行してください。</p>
                @endforelse
            </main>

            <aside class="w-full md:w-72 md:shrink-0 md:sticky md:top-25">
                <div class="bg-white rounded-lg p-5 shadow-sm">
                    <h2 class="text-sm font-bold mb-4 pb-2 border-b-2 border-[#3ea8ff]">人気記事ランキング</h2>
                    @foreach ($popular as $i => $article)
                        <div class="flex gap-3 items-start mb-3 last:mb-0">
                            <span class="text-lg font-bold text-[#3ea8ff] w-5 shrink-0">{{ $i + 1 }}</span>
                            <div>
                                <a href="{{ $article->url }}" target="_blank" rel="noopener"
                                   class="text-sm text-gray-900 hover:text-[#3ea8ff] leading-snug block no-underline">
                                    {{ $article->title }}
                                </a>
                                <div class="mt-1 text-sm text-red-400">♥ {{ $article->liked_count }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>

        </div>
    </div>

</body>
</html>
