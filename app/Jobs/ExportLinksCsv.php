<?php

namespace App\Jobs;

use App\Link;
use App\User;
use Common\Csv\BaseCsvExportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ExportLinksCsv extends BaseCsvExportJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $forUser;

    /**
     * @var int
     */
    protected $requesterId;

    public function __construct(int $requesterId, User $forUser = null)
    {
        $this->forUser = $forUser;
        $this->requesterId = $requesterId;
    }

    public function cacheName(): string
    {
        $cacheName = 'links';
        if ($this->forUser) {
            $cacheName .= ".{$this->forUser->id}";
        }
        return $cacheName;
    }

    protected function notificationName(): string
    {
        return 'links';
    }

    protected function generateLines()
    {
        $selectCols = [
            'id',
            'name',
            'alias',
            'hash',
            'long_url',
            'type',
            'expires_at',
            'clicks_count',
            'description',
            'created_at',
        ];

        $builder = $this->forUser ? $this->forUser->links() : app(Link::class);

        $builder
            ->select($selectCols)
            ->with('domain')
            ->chunkById(100, function (Collection $chunk) {
                $chunk->each(function (Link $link) {
                    $data = $link->toArray();
                    unset($data['has_password'], $data['id'], $data['hash'], $data['alias']);
                    $this->writeLineToCsv($data);
                });
            });
    }
}
