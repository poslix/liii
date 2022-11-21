<?php

namespace Database\Seeders;

use App\Link;
use App\LinkeableClick;
use App\LinkGroup;
use App\User;
use Common\Comments\Comment;
use Common\Workspaces\Workspace;
use DB;
use Illuminate\Database\Seeder;

class LocalSeeder extends Seeder
{
    public function run()
    {
        //$testUser = User::findAdmin();
        DB::beginTransaction();

//        $range = range(1, 10);
//        foreach ($range as $r) {
//            LinkClick::factory()->count(40000)->create();
//        }


//        $workspaces = Workspace::factory()->count(5)->create([
//            'owner_id' => $testUser->id,
//        ]);

//        $range = range(1, 10);
//            foreach ($range as $r) {
//                $links = Link::factory()->count(10000)->create([
//                    'user_id' => $testUser->id,
//                    //'workspace_id' => $workspaces->random()->id,
//                ]);

//        LinkClick::factory()->count(1000)->create([
//            'link_id' => $links->slice(0, 15)->random()->id,
//        ]);

//                LinkGroup::factory()->count(10000)->create([
//                    'user_id' => $testUser->id,
//                ]);
//            }

//        $ids = collect(range(1, 10000));
//        $ids->chunk(1000)->each(function ($chunk) {
//            $ids = $chunk->map(function($id) {
//                return ['link_id' => $id, 'link_group_id' => $id];
//            });
//            DB::table('link_group_link')->insert($ids->toArray());
//        });


        DB::commit();
    }
}
