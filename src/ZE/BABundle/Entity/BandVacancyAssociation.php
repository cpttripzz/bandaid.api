<?php

namespace ZE\BABundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * BandVacancyAssociation
 *
 * @ORM\Table(name="band_vacancy_association")
 * @ORM\Entity
 */
class BandVacancyAssociation
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
     * @ORM\ManyToOne(targetEntity="Band", inversedBy="bandVacancyAssociations",cascade={"persist"})
     */
    private $band;

    /**
     * @ORM\ManyToOne(targetEntity="BandVacancy", inversedBy="bandVacancyAssociations",cascade={"persist"})
     */
    private $bandVacancy;


    /**
     * Get id.

     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set band.

     *
     * @param \ZE\BABundle\Entity\Band $band
     *
     * @return BandVacancyAssociation
     */
    public function setBand(\ZE\BABundle\Entity\Band $band = null)
    {
        $this->band = $band;

        return $this;
    }

    /**
     * Get band.

     *
     * @return \ZE\BABundle\Entity\Band
     */
    public function getBand()
    {
        return $this->band;
    }


    /**
     * Set bandVacancy.

     *
     * @param \ZE\BABundle\Entity\BandVacancy $bandVacancy
     *
     * @return BandVacancyAssociation
     */
    public function setBandVacancy(\ZE\BABundle\Entity\BandVacancy $bandVacancy = null)
    {
        $this->bandVacancy = $bandVacancy;

        return $this;
    }

    /**
     * Get bandVacancy.

     *
     * @return \ZE\BABundle\Entity\BandVacancy
     */
    public function getBandVacancy()
    {
        return $this->bandVacancy;
    }
}
