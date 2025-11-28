<?php 
namespace Woweb\Zaakdms\Rule;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class FileSize implements Rule
{
    public $maxSize;

    private $disk;

    public function __construct(?string $disk = null)
    {
        $this->maxSize = config('zaakdms.max_file_size');
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
        return Storage::disk($this->disk)->size($value) <= $this->maxSize ? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must have a max file size of ' . $this->maxSize .' bytes.';
    }
}