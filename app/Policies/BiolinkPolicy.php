<?php

namespace App\Policies;

use App\Biolink;

class BiolinkPolicy extends LinkGroupPolicy
{
    protected $resource = Biolink::class;
}
