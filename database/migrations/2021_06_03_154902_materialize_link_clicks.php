<?php

use App\Link;
use Illuminate\Database\Migrations\Migration;

class MaterializeLinkClicks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cursor = DB::table('link_clicks')
            ->where('crawler', false)
            ->select(['link_clicks.*', DB::raw('count(*) as clicks_count')])
            ->groupBy('link_id')
            ->cursor();

        foreach ($cursor as $linkClicks) {
            Link::where('id', $linkClicks->link_id)->update([
                'clicks_count' => $linkClicks->clicks_count,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
