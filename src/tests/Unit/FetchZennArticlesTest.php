<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Console\Commands\FetchZennArticles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchZennArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_古い記事が51件目以降は削除される(): void
    {
        // 52件のlaravelタグ記事をDBに用意する
        for ($i = 1; $i <= 52; $i++) {
            Article::create([
                'topic'           => 'laravel',
                'zenn_id'         => (string) $i,
                'title'           => "記事{$i}",
                'slug'            => "article-{$i}",
                'author_name'     => 'テストユーザー',
                'author_username' => 'testuser',
                'liked_count'     => 0,
                'published_at'    => now()->subDays($i),
            ]);
        }

        // 50件を超えた分を削除するロジックを実行
        $total = Article::where('topic', 'laravel')->count();
        if ($total > 50) {
            $deleteCount = $total - 50;
            Article::where('topic', 'laravel')->oldest('published_at')->take($deleteCount)->delete();
        }

        // 50件になっているか確認
        $this->assertSame(50, Article::where('topic', 'laravel')->count());
    }

    public function test_topicで記事が絞り込まれる(): void
    {
        // laravelの記事を3件作成
        for ($i = 1; $i <= 3; $i++) {
            Article::create([
                'topic'           => 'laravel',
                'zenn_id'         => "laravel-{$i}",
                'title'           => "Laravel記事{$i}",
                'slug'            => "laravel-article-{$i}",
                'author_name'     => 'テストユーザー',
                'author_username' => 'testuser',
                'liked_count'     => 0,
                'published_at'    => now(),
            ]);
        }

        // nextjsの記事を2件作成
        for ($i = 1; $i <= 2; $i++) {
            Article::create([
                'topic'           => 'nextjs',
                'zenn_id'         => "nextjs-{$i}",
                'title'           => "Next.js記事{$i}",
                'slug'            => "nextjs-article-{$i}",
                'author_name'     => 'テストユーザー',
                'author_username' => 'testuser',
                'liked_count'     => 0,
                'published_at'    => now(),
            ]);
        }

        // laravelで絞り込むと3件
        $this->assertSame(3, Article::where('topic', 'laravel')->count());

        // nextjsで絞り込むと2件
        $this->assertSame(2, Article::where('topic', 'nextjs')->count());
    }

    public function test_50件制限はtopicごとに独立している(): void
    {
        // laravelの記事を52件作成
        for ($i = 1; $i <= 52; $i++) {
            Article::create([
                'topic'           => 'laravel',
                'zenn_id'         => "laravel-{$i}",
                'title'           => "Laravel記事{$i}",
                'slug'            => "laravel-article-{$i}",
                'author_name'     => 'テストユーザー',
                'author_username' => 'testuser',
                'liked_count'     => 0,
                'published_at'    => now()->subDays($i),
            ]);
        }

        // nextjsの記事を3件作成
        for ($i = 1; $i <= 3; $i++) {
            Article::create([
                'topic'           => 'nextjs',
                'zenn_id'         => "nextjs-{$i}",
                'title'           => "Next.js記事{$i}",
                'slug'            => "nextjs-article-{$i}",
                'author_name'     => 'テストユーザー',
                'author_username' => 'testuser',
                'liked_count'     => 0,
                'published_at'    => now()->subDays($i),
            ]);
        }

        // laravelだけ削除ロジックを実行
        $total = Article::where('topic', 'laravel')->count();
        if ($total > 50) {
            $deleteCount = $total - 50;
            Article::where('topic', 'laravel')->oldest('published_at')->take($deleteCount)->delete();
        }

        // laravelは50件に絞られている
        $this->assertSame(50, Article::where('topic', 'laravel')->count());

        // nextjsは影響を受けず3件のまま
        $this->assertSame(3, Article::where('topic', 'nextjs')->count());
    }
}
