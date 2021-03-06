<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CreateCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Log::channel('event')->info('iam er',[]);

        return [
            'content' => 'required|min:1|max:1000',
            'type' => ['required', Rule::in(['parent', 'reply'])],
            'commentable' => 'required|integer|min:0',
            'references' => 'array',
            'references.*' => 'integer',
        ];
    }
}
