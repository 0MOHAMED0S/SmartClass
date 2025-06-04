<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $icons = [
            'https://cdn-icons-png.flaticon.com/512/3593/3593422.png',
            'https://cdn-icons-png.flaticon.com/512/3593/3593441.png',
            'https://cdn-icons-png.flaticon.com/512/3593/3593462.png',
            'https://cdn-icons-png.flaticon.com/512/9072/9072569.png',
            'https://cdn-icons-png.flaticon.com/512/2354/2354280.png',
            'https://cdn-icons-png.flaticon.com/512/3449/3449632.png',
            'https://cdn-icons-png.flaticon.com/512/185/185570.png',
            'https://cdn-icons-png.flaticon.com/512/6660/6660279.png',
            'https://cdn-icons-png.flaticon.com/512/5608/5608848.png',
            'https://cdn-icons-png.flaticon.com/512/3874/3874104.png',
            'https://cdn-icons-png.flaticon.com/512/167/167707.png',
            'https://cdn-icons-png.flaticon.com/512/3103/3103446.png',
            'https://cdn-icons-png.flaticon.com/512/1995/1995581.png',
            'https://cdn-icons-png.flaticon.com/512/906/906175.png',
            'https://cdn-icons-png.flaticon.com/512/942/942748.png',
            'https://cdn-icons-png.flaticon.com/512/3515/3515781.png',
            'https://cdn-icons-png.flaticon.com/512/3095/3095583.png',
            'https://cdn-icons-png.flaticon.com/512/6816/6816631.png',
            'https://cdn-icons-png.flaticon.com/512/747/747310.png',
            'https://cdn-icons-png.flaticon.com/512/10714/10714533.png',
            'https://cdn-icons-png.flaticon.com/512/609/609803.png',
            'https://cdn-icons-png.flaticon.com/512/545/545705.png',
            'https://cdn-icons-png.flaticon.com/512/2985/2985168.png',
            'https://cdn-icons-png.flaticon.com/512/2991/2991108.png',
            'https://cdn-icons-png.flaticon.com/512/3062/3062634.png',
            'https://cdn-icons-png.flaticon.com/512/2474/2474530.png',
            'https://cdn-icons-png.flaticon.com/512/4954/4954819.png',
            'https://cdn-icons-png.flaticon.com/512/1828/1828645.png',
        ];
        return [
        'name' => 'required|string|max:25',
        'path' => ['required', 'url', Rule::in($icons)],
        ];
    }
}
