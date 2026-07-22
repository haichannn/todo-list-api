<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Attributes\Collects;
use Illuminate\Http\Resources\Json\ResourceCollection;

#[Collects(TodoResource::class)]
class TodoCollection extends ResourceCollection
{
    //
}
