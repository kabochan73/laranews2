<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchZennArticles extends Command
{
    protected $signature = 'zenn:fetch {topic : laravelまたはnextjs}';

    protected $description = 'ZennからトピックごとのTagの記事を取得して保存する';

    public function handle(): int
    {
        $topic = $this->argument('topic');

        $this->info("{$topic}の記事を取得中...");

        $response = Http::get('https://zenn.dev/api/articles', [
            'topicname' => $topic,
            'order' => 'latest',
            'count' => 30,
        ]);

        if ($response->failed()) {
            $this->error('Zenn APIの取得に失敗しました。');
            return Command::FAILURE;
        }

        $articles = $response->json('articles', []);
        $saved = 0;

        foreach ($articles as $data) {
            Article::updateOrCreate(
                ['zenn_id' => (string) $data['id']],
                [
                    'topic' => $topic,
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'emoji' => $data['emoji'] ?? null,
                    'author_name' => $data['user']['name'],
                    'author_username' => $data['user']['username'],
                    'liked_count' => $data['liked_count'] ?? 0,
                    'published_at' => $data['published_at'],
                ]
            );
            $saved++;
        }

        $this->info("{$saved}件の記事を保存しました。");

        $total = Article::where('topic', $topic)->count();
        if ($total > 50) {
            $deleteCount = $total - 50;
            Article::where('topic', $topic)->oldest('published_at')->take($deleteCount)->delete();
            $this->info("古い記事を{$deleteCount}件削除しました。");
        }

        return Command::SUCCESS;
    }
}
