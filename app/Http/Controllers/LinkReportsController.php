<?php

namespace App\Http\Controllers;

use App\Actions\Link\GenerateLinkReport;
use Auth;
use Common\Core\BaseController;
use Common\Workspaces\ActiveWorkspace;
use Illuminate\Http\Request;

class LinkReportsController extends BaseController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var ActiveWorkspace
     */
    private $activeWorkspace;

    public function __construct(
        Request $request,
        ActiveWorkspace $activeWorkspace
    ) {
        $this->request = $request;
        $this->activeWorkspace = $activeWorkspace;
    }

    public function show()
    {
        $reports = app(GenerateLinkReport::class)->execute(
            $this->request->all(),
            $this->activeWorkspace->personal()
                ? Auth::user()
                : $this->activeWorkspace->workspace(),
        );

        return $this->success(['analytics' => $reports]);
    }
}
