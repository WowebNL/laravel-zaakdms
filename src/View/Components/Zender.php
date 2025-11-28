<?php

namespace Woweb\Zaakdms\View\Components;

use Illuminate\View\Component;

class Zender extends Component
{
    /**
     * The application name of the sender
     *
     * @var string
     */
    public $application;

    /**
     * The administration who is sending
     *
     * @var string
     */
    public $administrative;

    /**
     * The organisation who is sending
     *
     * @var string
     */
    public $organization;

    /**
     * The user who is sending
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
        $sender = config('zaakdms.sender');

        $this->application = $sender['application'];
        $this->administrative = $sender['administrative'];
        $this->organization = $sender['organization'];
        $this->user = $sender['user'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('zaakdms::components.zender');
    }
}