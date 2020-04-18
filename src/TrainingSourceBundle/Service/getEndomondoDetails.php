<?php

namespace TrainingSourceBundle\Service;

class getEndomondoDetails{
    
    function get($url){

	$url = $this->api_url($url);
	$json = file_get_contents($url);

        $training = json_decode($json);
	
	return $training;
    }
    private function api_url($url){
	preg_match("/https?:\/\/(www|app)\.endomondo\.com\/users\/(\d+)\/workouts\/(\d+)/",$url,$out);
	if(count($out) == 4){
	    return 'https://www.endomondo.com/rest/v1/users/'.$out[2].'/workouts/'.$out[3];
	}

	preg_match("/https?:\/\/(www|app)\.endomondo\.com\/workouts\/(\d+)\/(\d+)/",$url,$out);
	if(count($out) == 4){
	    return 'https://www.endomondo.com/rest/v1/users/'.$out[3].'/workouts/'.$out[2];
	}

	$trescBledu = "Użyj linku endomondo w jednym z poniższych formatów:<br/>";
	$trescBledu .= "https://www.endomondo.com/users/17823172/workouts/419854310<br/>https://www.endomondo.com/workouts/419854310/17823172<br/>http://app.endomondo.com/workouts/419854310/17823172";
	$trescBledu .= "<br/>Jeśli mimo tego masz działający link w innym formacie, możesz poprosić <a href=\"http://www.wykop.pl/ludzie/Robuz/\">@Robuz</a> o uwzględnienie go w skrypcie";
	throw new \Exception('Unrecognised url format.');
    }
}