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
    private string $videoPrivacy         = 'anybody';

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

    public function videoPrivacy(string $privacy) : self
    {
        $this->videoPrivacy = $privacy;

        return $this;
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            if(!$this->isNullValue($value)) {
                $model->{$attribute} = $this->uploadVideo($request, $value);
                VideoUploaded::dispatch($model, $value);
            }
            else {
                $model->{$attribute} = null;
            }

        }
    }

    protected function uploadVideo(NovaRequest $request, string $filename) : int
    {
        $uri = \Vimeo::upload(
            \Storage::path('tmp/videos/' . $filename),
            [
                'name'         => $request->input($this->titleAttribute),
                'description'  => $request->input($this->descriptionAttribute),
                'privacy.view' => $this->videoPrivacy,
            ]
        );

        // Just grab the id and convert to int.
        return (int)substr($uri, strlen('/videos/'));
    }


}
