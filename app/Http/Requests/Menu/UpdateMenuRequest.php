<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menuId = $this->route('id') ?? $this->route('menu');

        return [
            'name' => 'required_without:nama_menu|string|max:50',
            'nama_menu' => 'required_without:name|string|max:50',
            'route' => 'nullable|string|max:50',
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('menus', 'slug')->ignore($menuId),
            ],
            'icon_id' => 'nullable|exists:menu_icons,id',
            'position' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'parent_id' => 'nullable|exists:menus,id',
            'is_active' => 'nullable|boolean',
        ];
    }
}
