<?php

namespace ZE\BABundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * @ORM\Entity
 * @ORM\Table(name="genre")
 */
class Genre
{
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
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;


    /**
     * @Gedmo\Slug(fields={"name" })
     * @ORM\Column(unique=true)
     */
    private $slug;
    public function __toString()
    {
        return $this->name;
    }

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
     * Set name
     *
     * @param string $name
     * @return Genre
     */
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
    /** @ORM\ManyToMany(targetEntity="Association", mappedBy="genres") **/
    protected $associations;

    /** @ORM\ManyToMany(targetEntity="BandVacancy", mappedBy="genres") **/
    protected $bandVacancies;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->associations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add associations
     *
     * @param \ZE\BABundle\Entity\Association $associations
     *
     * @return Genre
     */
    public function addAssociation(\ZE\BABundle\Entity\Association $associations)
    {
        $this->associations[] = $associations;

        return $this;
    }

    /**
     * Remove associations
     *
     * @param \ZE\BABundle\Entity\Association $associations
     */
    public function removeAssociation(\ZE\BABundle\Entity\Association $associations)
    {
        $this->associations->removeElement($associations);
    }

    /**
     * Get associations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Genre
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
}
