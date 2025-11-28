<?php 
namespace Woweb\Zaakdms\Rule;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class FileExists implements Rule
{
    private $disk;

    public function __construct(?string $disk = null) 
    {
        $this->disk = $disk;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Storage::disk($this->disk)->exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute bestaat niet op de disk ' . $this->disk;
    }
}