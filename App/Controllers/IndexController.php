<?php

namespace Controllers;

use Framework\Box;
use Framework\View;

class IndexController extends \Framework\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->locale->load('Index', Box::getApplication()['language']);
    }

    public function indexAction()
    {
        Box::$data['title']           = $this->locale->get('welcome_text');
        Box::$data['welcome_message'] = $this->locale->get('welcome_message');

        View::renderDefault('index/index');
    }

    public function subpageAction()
    {
        Box::$data['title']           = $this->locale->get('subpage_text');
        Box::$data['welcome_message'] = $this->locale->get('subpage_message');

        View::renderLayout('header');
        View::render('index/subpage');
        View::renderLayout('footer');

    }
}
