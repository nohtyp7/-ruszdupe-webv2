<?php

namespace WykopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Distance
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="WykopBundle\Entity\DistanceRepository")
 */
class Distance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var float
     *
     * @ORM\Column(name="distance", type="float")
     */
    private $distance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var float
     *
     * @ORM\Column(name="avg_speed", type="float", nullable=true)
     */
    private $avgSpeed;

    /**
     * @var integer
     *
     * @ORM\Column(name="calories", type="integer", nullable=true)
     */
    private $calories;
    
    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text", nullable=true)
     */
    private $details;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Distance
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set distance
     *
     * @param float $distance
     * @return Distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Distance
     */
    public function setStartDate($startDate)
    {
	if(is_string($startDate)){
	    $this->startDate = new \DateTime($startDate);
	}else{
	    $this->startDate = $startDate;
	}

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Distance
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set avgSpeed
     *
     * @param float $avgSpeed
     * @return Distance
     */
    public function setAvgSpeed($avgSpeed)
    {
        $this->avgSpeed = $avgSpeed;

        return $this;
    }

    /**
     * Get avgSpeed
     *
     * @return float 
     */
    public function getAvgSpeed()
    {
        return $this->avgSpeed;
    }

    /**
     * Set calories
     *
     * @param integer $calories
     * @return Distance
     */
    public function setCalories($calories)
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Get calories
     *
     * @return integer 
     */
    public function getCalories()
    {
        return $this->calories;
    }
    
    /**
     * Set details
     *
     * @param string $details
     * @return Distance
     */
    public function setDetails($details)
    {
        $this->details= json_encode($details);

        return $this;
    }

    /**
     * Get details
     *
     * @return JSON
     */
    public function getDetails()
    {
        return json_decode($this->details);
    }
}
