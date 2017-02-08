<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGirlOfTheWeekRequest extends FormRequest
{
    public function __construct()
    {
        parent::__construct();

        // @note The thumbnail data seems to be too heavy to flash
        $this->dontFlash[] = 'media-video-thumbnail';
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'country' => 'required',
            'place' => 'required',
            'label' => 'required',
            'media.*.file' => 'mimetypes:image/gif,image/png,image/jpeg,image/bmp,video/mp4,video/x-m4v,video/webm,video/ogg,video/avi,video/mpeg,video/quicktime|max:' . 1024 * 1000
        ];
    }

    public function messages()
    {
        return [];
    }
}
