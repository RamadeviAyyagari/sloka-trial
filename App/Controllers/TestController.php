<?php
namespace Controllers;

use Framework\Controller;
use Framework\View;
use Framework\Box;
use Models\UsersModel;

class TestController extends Controller {

    public function indexAction() {
        Box::$data['name'] = 'Ramadevi';
        View::renderDefault('test/index');
    }

    public function infoAction(){

        $usersModel = new UsersModel();
        Box::$data['userDetails']  = $usersModel->getUserDetails(33);

        if(Box::$data['userDetails']['age'] < 18){
            Box::$data['message'] =' You can not vote ';
        } else {
            Box::$data['message'] = ' Please vote ';
        }

        View::renderDefault('test/info');
    }
}