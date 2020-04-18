<?php

namespace TrainingSourceBundle\Service;

class getStravaDetails{
    private $_token;
    
    function __construct($token){
	$this->_token = $token;
    }
    
    function get($url){
	$url = $this->api_url($url);
	
	$training = json_decode(file_get_contents($url));
	
	return $training;
    }
    
    private function api_url($url){
	$url = explode('/', $url);
	if(count($url) == 5)
	    return 'https://www.strava.com/api/v3/activities/' . $url[4] . '?access_token='.$this->_token;
	else
	    throw new \Exception('Unrecognised url format.');
    }
}