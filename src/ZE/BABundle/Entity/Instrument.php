<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 22/03/14
 * Time: 19:46
 */

namespace ZE\BABundle\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;


    /**
    * @ORM\Entity
    * @ORM\Table(name="instrument")
    */
class Instrument
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected  $id;


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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;


    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(unique=true)
     */
    private $slug;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /** @ORM\ManyToMany(targetEntity="Musician", mappedBy="instruments") **/
    private $musicians;

    /** @ORM\ManyToMany(targetEntity="BandVacancy", mappedBy="instruments") **/
    protected $bandVacancies;

    public function setMusician(Musician $musician)
    {
        $this->musician = $musician;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->musicians = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add musicians
     *
     * @param \ZE\BABundle\Entity\Musician $musicians
     *
     * @return Instrument
     */
    public function addMusician(\ZE\BABundle\Entity\Musician $musicians)
    {
        $this->musicians[] = $musicians;

        return $this;
    }

    /**
     * Remove musicians
     *
     * @param \ZE\BABundle\Entity\Musician $musicians
     */
    public function removeMusician(\ZE\BABundle\Entity\Musician $musicians)
    {
        $this->musicians->removeElement($musicians);
    }

    /**
     * Get musicians
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMusicians()
    {
        return $this->musicians;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Instrument
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add bandVacancy.

     *
     * @param \ZE\BABundle\Entity\BandVacancy $bandVacancy
     *
     * @return Instrument
     */
    public function addBandVacancy(\ZE\BABundle\Entity\BandVacancy $bandVacancy)
    {
        $this->bandVacancies[] = $bandVacancy;

        return $this;
    }

    /**
     * Remove bandVacancy.

     *
     * @param \ZE\BABundle\Entity\BandVacancy $bandVacancy
     */
    public function removeBandVacancy(\ZE\BABundle\Entity\BandVacancy $bandVacancy)
    {
        $this->bandVacancies->removeElement($bandVacancy);
    }

    /**
     * Get bandVacancies.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBandVacancies()
    {
        return $this->bandVacancies;
    }
}
