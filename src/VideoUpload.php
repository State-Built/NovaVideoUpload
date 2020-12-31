<?php

namespace State\VideoUpload;

use App\Film\VideoProvider;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class VideoUpload extends Field
{
    public         $component            = 'video-upload';
    private string $titleAttribute       = 'title';
    private string $descriptionAttribute = 'description';

    public function titleAttribute(string $attribute) : self
    {
        $this->titleAttribute = $attribute;

        return $this;
    }

    public function descriptionAttribute(string $attribute) : self
    {
        $this->descriptionAttribute = $attribute;

        return $this;
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            $model->{$attribute} = $this->isNullValue($value) ?
                null :
                $this->uploadVideo($request, $value);

            VideoUploaded::dispatch($model, $value);
        }
    }

    protected function uploadVideo(NovaRequest $request, string $filename) : int
    {
        $uri = \Vimeo::upload(
            \Storage::path('tmp/videos/' . $filename),
            ['name' => $request->input($this->titleAttribute), 'description' => $request->input($this->descriptionAttribute)]
        );

        // Just grab the id and convert to int.
        return (int)substr($uri, strlen('/videos/'));
    }


}
