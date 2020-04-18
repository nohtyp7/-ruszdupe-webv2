<?php

namespace WykopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LastDistance
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="WykopBundle\Entity\LastDistanceRepository")
 */
class LastDistance
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
     * @var float
     *
     * @ORM\Column(name="distance", type="float")
     */
    private $distance;

    /**
     * @var integer
     * 
     * @ORM\ManyToOne(targetEntity="Tag")
     * @ORM\JoinColumn(name="id_tag", )
     */
    private $tag;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * Set Tag
     *
     * @param Tag $tag
     * @return OstatniDystans
     */
    public function setTag($tag)
    {
        $this->tag= $tag;

        return $this;
    }

    /**
     * Get Tag
     *
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set distance
     *
     * @param integer $distance
     * @return OstatniDystans
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return OstatniDystans
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}
