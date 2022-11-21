<?php

namespace App\Http\Controllers;

use App\Actions\Biolink\AddInitialContentToBiolink;
use App\Actions\Biolink\LoadBiolinkContent;
use App\Biolink;
use App\BiolinkWidget;
use App\Http\Requests\CrupdateLinkGroupRequest;
use App\Link;
use App\LinkGroup;
use Auth;
use DB;
use Illuminate\Http\Request;

class BiolinkController extends LinkGroupController
{
    public function __construct(Biolink $model, Request $request)
    {
        parent::__construct($model, $request);
    }

    public function store(CrupdateLinkGroupRequest $request)
    {
        $response = parent::store($request);
        $biolink = $response->getOriginalContent()['biolink'];

        app(AddInitialContentToBiolink::class)->execute($biolink['id']);

        return $response;
    }

    public function show(LinkGroup $biolink)
    {
        $this->authorize('show', $biolink);

        $biolink = app(LoadBiolinkContent::class)->execute(
            $biolink,
            $this->request->all(),
        );

        return $this->success(['biolink' => $biolink]);
    }

    public function detachContent(Biolink $biolink)
    {
        $this->authorize('update', $biolink);

        $data = $this->validate($this->request, [
            'contentItem.id' => 'required|int',
            'contentItem.model_type' => 'required|string',
            'contentItem.position' => 'required|int',
        ]);

        // decrement link positions by 1
        $biolink
            ->links()
            ->where('position', '>', $data['contentItem']['position'])
            ->update(['position' => DB::raw('position - 1')]);

        // decrement widget positions by 1
        $biolink
            ->widgets()
            ->where('position', '>', $data['contentItem']['position'])
            ->decrement('position');

        if ($data['contentItem']['model_type'] === Link::MODEL_TYPE) {
            $biolink->links()->detach($data['contentItem']['id']);
        } else {
            $biolink
                ->widgets()
                ->where('id', $data['contentItem']['id'])
                ->delete();
        }

        return $this->success();
    }
}
