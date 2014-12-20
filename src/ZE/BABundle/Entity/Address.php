<?php

namespace ZE\BABundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * Address
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="ZE\BABundle\Entity\Repository\Address")
 */
class Address
{

    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string",  nullable=true)
     */
    private $address;


    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="float", precision=7, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="float", precision=7, nullable=true)
     */
    private $longitude;

    /**
     * @var \City
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;


    /**
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

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
     * Set address
     *
     * @param string $address
     *
     * @return Address
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Address
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Address
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set city
     *
     * @param \ZE\BABundle\Entity\City $city
     *
     * @return Address
     */
    public function setCity(\ZE\BABundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \ZE\BABundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    public function __toString()
    {
        return $this->address . ' ' . $this->getCity()->getName();
    }

    public function getLongName()
    {
        $region = $this->getRegion();
        if (!empty($region)){
            $region = $this->getRegion()->getShortName() . ' ';
        }

        $name = $this->address . ' ' . $this->getCity()->getName() .' ' . $region
            . $this->getCity()->getCountry()->getName() ;
        return $name;
    }

    /** @ORM\ManyToMany(targetEntity="Association", mappedBy="addresses") **/
    protected $associations;

   

    /**
     * Set region
     *
     * @param \ZE\BABundle\Entity\Region $region
     *
     * @return Address
     */
    public function setRegion(\ZE\BABundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \ZE\BABundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->associations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add association.

     *
     * @param \ZE\BABundle\Entity\Association $association
     *
     * @return Address
     */
    public function addAssociation(\ZE\BABundle\Entity\Association $association)
    {
        $this->associations[] = $association;

        return $this;
    }

    /**
     * Remove association.

     *
     * @param \ZE\BABundle\Entity\Association $association
     */
    public function removeAssociation(\ZE\BABundle\Entity\Association $association)
    {
        $this->associations->removeElement($association);
    }

    /**
     * Get associations.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssociations()
    {
        return $this->associations;
    }
}
