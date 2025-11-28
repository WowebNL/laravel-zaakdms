<?php
namespace Woweb\Zaakdms\Rule;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class RequiredWithoutInitiatorIdentificatie implements Rule
{
    private $options;
    /**
     * @var string
     */
    private $type;

    public function __construct(string $type, array $options)
    {
        $this->type = $type;
        $this->options = $options;
    }    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!$value) {
            if(!isset($this->options['initiator'][$this->type]['identificatie']) || $this->options['initiator'][$this->type]['identificatie']) {
                \Validator::make([$attribute => $value],
                    [
                        $attribute => 'numeric|digits:9'
                    ]);
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
        return 'The :attribute must be present if no identificatie attribute is set.';
    }
}
