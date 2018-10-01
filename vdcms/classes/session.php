<?php

class Session {

    protected static $instance;
    var $SessionName = 'VDSession';
    private function __construct() 
    {
	session_start();
	
	Session::$instance = $this;
    }
    
    public static function getInstance()
    {
	if (Session::$instance === NULL)
	{
	    new Session;
	}
	return Session::$instance;
    }
    
    public function Set($Setting, $Value)
    {
	$_SESSION[$this->SessionName][$Setting] = $Value;
    }
    
    public function is_Set($Setting)
    {
	if (isset($_SESSION[$this->SessionName][$Setting]) &&
	    !empty($_SESSION[$this->SessionName][$Setting]))
		return true;
	return false;
    }

    public function & Get($Setting, $Default='')
    {
	if ($this->is_Set($Setting))
	    return $_SESSION[$this->SessionName][$Setting];
	else
	    return $Default;
    }
    
}