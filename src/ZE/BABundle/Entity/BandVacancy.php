<?php

namespace ZE\BABundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
/**
 *
 * @ORM\Table(name="band_vacancy")
 * @ORM\Entity
 */
class BandVacancy
{
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
     * @ORM\OneToMany(targetEntity="BandVacancyAssociation", mappedBy="bandVacancy",cascade={"persist"})
     */
    private $bandVacancyAssociations;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="bandVacancies",cascade={"persist"})
     * @ORM\JoinTable(name="bandvacancy_genre",
     *   joinColumns={@ORM\JoinColumn(name="bandvacancy_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     * )
     */
    protected $genres;


    /**
     * @ORM\ManyToMany(targetEntity="Instrument", inversedBy="bandVacancies",cascade={"persist"})
     * @ORM\JoinTable(name="bandvacancy_instrument",
     *   joinColumns={@ORM\JoinColumn(name="bandvacancy_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="instrument_id", referencedColumnName="id")}
     * )
     */
    protected $instruments;

    /** @ORM\Column(name="comment", type="text",nullable=true) */
    private $comment;

    /** @ORM\Column(name="name", type="text",nullable=true) */
    private $name;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bandVacancyAssociations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instruments = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function __toString()
    {
        return 'genres: ' .implode(',',$this->genres->toArray()) . ' instruments:'. implode(',',$this->instruments->toArray());
    }

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
     * Set name.

     *
     * @param \DateTime $name
     *
     * @return BandVacancy
     */
    public function setComment($name)
    {
        $this->comment = $name;

        return $this;
    }

    /**
     * Get name.

     *
     * @return \DateTime
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add bandVacancyAssociation.

     *
     * @param \ZE\BABundle\Entity\BandVacancyAssociation $bandVacancyAssociation
     *
     * @return BandVacancy
     */
    public function addBandVacancyAssociation(\ZE\BABundle\Entity\BandVacancyAssociation $bandVacancyAssociation)
    {
        $this->bandVacancyAssociations[] = $bandVacancyAssociation;

        return $this;
    }

    /**
     * Remove bandVacancyAssociation.

     *
     * @param \ZE\BABundle\Entity\BandVacancyAssociation $bandVacancyAssociation
     */
    public function removeBandVacancyAssociation(\ZE\BABundle\Entity\BandVacancyAssociation $bandVacancyAssociation)
    {
        $this->bandVacancyAssociations->removeElement($bandVacancyAssociation);
    }

    /**
     * Get bandVacancyAssociations.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBandVacancyAssociations()
    {
        return $this->bandVacancyAssociations;
    }

    /**
     * Add genre.

     *
     * @param \ZE\BABundle\Entity\Genre $genre
     *
     * @return BandVacancy
     */
    public function addGenre(\ZE\BABundle\Entity\Genre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genre.

     *
     * @param \ZE\BABundle\Entity\Genre $genre
     */
    public function removeGenre(\ZE\BABundle\Entity\Genre $genre)
    {
        $this->genres->removeElement($genre);
    }

    /**
     * Get genres.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Add instrument.

     *
     * @param \ZE\BABundle\Entity\Instrument $instrument
     *
     * @return BandVacancy
     */
    public function addInstrument(\ZE\BABundle\Entity\Instrument $instrument)
    {
        $this->instruments[] = $instrument;

        return $this;
    }

    /**
     * Remove instrument.

     *
     * @param \ZE\BABundle\Entity\Instrument $instrument
     */
    public function removeInstrument(\ZE\BABundle\Entity\Instrument $instrument)
    {
        $this->instruments->removeElement($instrument);
    }

    /**
     * Get instruments.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInstruments()
    {
        return $this->instruments;
    }

    /**
     * Set name.

     *
     * @param string $name
     *
     * @return BandVacancy
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.

     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
