<?php


namespace State\VideoUpload;


use Illuminate\Foundation\Events\Dispatchable;

class VideoUploaded
{
    use Dispatchable;

    public function __construct(public $model, public $filename)
    {
    }

}
