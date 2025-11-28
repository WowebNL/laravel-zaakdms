<?php
namespace Woweb\Zaakdms\Rule;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ValidateInitiatorField implements Rule
{
    private $options;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $fieldName;

    public function __construct(string $type, string $fieldName, array $options)
    {
        $this->type = $type;
        $this->fieldName = $fieldName;
        $this->options = $options;
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
        if($this->options['initiator']['type'] == $this->type) {
            if(!isset($this->options['initiator'][$this->type][$this->fieldName]) ||!$this->options['initiator'][$this->type][$this->fieldName]) {
                if(!empty($value)) {
                    return true;
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be present if for the initiator.';
    }
}
