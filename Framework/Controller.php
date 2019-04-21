<?php

/**
 * Controller - base controller
 */

namespace Framework;

abstract class Controller
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var View $view
     */
    public $view;

    /**
     * @var Locale
     */
    protected $locale;


    public function __construct()
    {
        $this->view = new View();

        $this->locale = new Locale();
    }
}
