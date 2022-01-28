<?php

namespace State\VideoUpload;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

// todo Create validation that video is uploaded.
class VideoUpload extends Field
{
    public         $component            = 'video-upload';
    private string $titleAttribute       = 'title';
    private string $descriptionAttribute = 'description';
    private string $videoPrivacy         = 'anybody';
    private string $embedPrivacy         = 'whitelist';
    private string $providerIdAttribute  = 'provider_id';


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

    public function embedPrivacy(string $privacy) : self
    {
        $this->embedPrivacy = $privacy;

        return $this;
    }

    public function providerIdAttribute(string $providerAttribute) : self
    {
        $this->providerIdAttribute = $providerAttribute;

        return $this;
    }

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            if (!$this->isNullValue($value)) {

                // exit if we're not changing anything.
                if ($value == $model->{$this->providerIdAttribute}) {
                    return;
                }

                // update id if that's all we're doing
                if ($this->isVimeoId($value)) {
                    $model->{$attribute} = $value;
                }
                else { // otherwise upload.
                    if ($request->isCreateOrAttachRequest()) { // fresh upload on create.
                        $model->{$attribute} = $this->uploadVideo($request, $value);
                    }
                    else { // upload replacement otherwise.
                        $this->replaceVideo($request, $value, $model);
                    }
                }
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
                'name'        => $request->input($this->titleAttribute),
                'description' => $request->input($this->descriptionAttribute),
                'privacy'     => ['view' => $this->videoPrivacy, 'embed' => $this->embedPrivacy],
            ]
        );

        return $this->getVimeoIdFromUri($uri);
    }

    protected function replaceVideo(NovaRequest $request, string $filename, $model)
    {
        \Vimeo::replace(
            '/videos/' . $model->{$this->providerIdAttribute},
            \Storage::path('tmp/videos/' . $filename),
            [
                'name'        => $request->input($this->titleAttribute),
                'description' => $request->input($this->descriptionAttribute),
                'privacy'     => ['view' => $this->videoPrivacy],
            ],
        );
    }

    protected function getVimeoIdFromUri($uri) : int
    {
        return (int) substr($uri, strlen('/videos/'));
    }

    private function isVimeoId($value) : bool
    {
        return (bool) (int) ($value);
    }

    public function meta()
    {
        return array_merge(parent::meta(), [
            'tusEndpoint' => route('nova.tus') . '/',
        ]);
    }

}
