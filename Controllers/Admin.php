<?php
class Admin extends Controller
{
    public function __construct() {
        parent::__construct();
        session_start();
    }
    public function index()
    {
        $data['title'] = 'Panel de administracion';
        $this->views->getView('admin', 'home', $data);
    }

}