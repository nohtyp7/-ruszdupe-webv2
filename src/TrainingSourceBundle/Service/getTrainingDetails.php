<?php

namespace TrainingSourceBundle\Service;

class getTrainingDetails {

    private $_url;
    private $_endomondo;
    private $_strava;
    private $_runkeeper;
    private $_training;

    function __construct($endomondo, $strava, $runkeeper) {
	$this->_endomondo = $endomondo;
	$this->_strava = $strava;
	$this->_runkeeper = $runkeeper;
    }

    private function setUrl($url) {
	$this->_url = trim($url);

	if( preg_match('/endo/', $url) ) {
	    $training = $this->_endomondo->get($this->_url);

	    $this->_training['training'] = $training;
	    $this->_training['url'] = $this->_url;

	    $this->_training['start_time'] = $training->local_start_time;
	    $this->_training['duration'] = round($training->duration);
	    $this->_training['distance'] = $training->distance;
	    if( isset($training->ascent) )
		$this->_training['ascent'] = $training->ascent;
	    if( isset($training->descent) )
		$this->_training['descent'] = $training->descent;
	    if( isset($training->ascent) && isset($training->descent) )
		$this->_training['distance_vertical'] = $training->ascent + $training->descent;
	    $this->_training['calories'] = round($training->calories);
	    $this->_training['speed_avg'] = $training->speed_avg;
	    if( isset($training->speed_max) )
		$this->_training['speed_max'] = $training->speed_max;
	    if( isset($training->heart_rate_avg) )
		$this->_training['heart_rate_avg'] = $training->heart_rate_avg;
	    if( isset($training->heart_rate_max) )
		$this->_training['heart_rate_max'] = $training->heart_rate_max;

	    if( isset($training->pictures) )
		$this->_training['pictures'] = $training->pictures;
	}elseif( preg_match('/strava/', $url) ) {
	    $training = $this->_strava->get($this->_url);

	    $this->_training['training'] = $training;
	    $this->_training['url'] = $this->_url;

	    $this->_training['start_time'] = $training->start_date;
	    $this->_training['duration'] = round($training->moving_time);
	    $this->_training['distance'] = $training->distance / 1000;
	    $this->_training['calories'] = round($training->kilojoules * 02.388458966);
	    $this->_training['speed_avg'] = $training->average_speed * 3.6;
	    $this->_training['speed_max'] = $training->max_speed * 3.6;
	    if( isset($training->average_heartrate) )
		$this->_training['heart_rate_avg'] = $training->average_heartrate;
	    if( isset($training->max_heartrate) )
		$this->_training['heart_rate_max'] = $training->max_heartrate;
	}else {
	    $this->_training['distance'] = $url;
	}
    }

    function get($url) {
	$this->_training = array();
	$this->setUrl($url);
	return $this->_training;
    }

}
