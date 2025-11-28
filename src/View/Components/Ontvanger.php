<?php

namespace Woweb\Zaakdms\View\Components;

use Illuminate\View\Component;

class Ontvanger extends Component
{
    /**
     * The application name of the receiver
     *
     * @var string
     */
    public $application;

    /**
     * The administration who is receiver
     *
     * @var string
     */
    public $administrative;

    /**
     * The organisation who is receiver
     *
     * @var string
     */
    public $organization;

    /**
     * The user who is receiver
     *
     * @var string
     */
    public $user;


    /**
     * Create the component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $receiver = config('zaakdms.receiver');

        $this->application = $receiver['application'];
        $this->administrative = $receiver['administrative'];
        $this->organization = $receiver['organization'];
        $this->user = $receiver['user'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('zaakdms::components.ontvanger');
    }
}