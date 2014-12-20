<?php


namespace ZE\BABundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="musician")
 * @ORM\Entity(repositoryClass="ZE\BABundle\Entity\Repository\Musician")
 */
class Musician extends Association
{

    /**
     * @ORM\ManyToMany(targetEntity="Instrument", inversedBy="musicians")
     * @ORM\JoinTable(name="musician_instrument",
     *   joinColumns={@ORM\JoinColumn(name="musician_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="instrument_id", referencedColumnName="id")}
     * )
     */
    protected $instruments;

    /** @ORM\ManyToMany(targetEntity="Band", mappedBy="musicians") **/
    protected $bands;

    public function __construct()
    {
        $this->instruments = new ArrayCollection();
        $this->bands = new ArrayCollection();
    }


    /**
     * Add instruments
     *
     * @param \ZE\BABundle\Entity\Instrument $instruments
     *
     * @return Musician
     */
    public function addInstrument(\ZE\BABundle\Entity\Instrument $instruments)
    {
        $this->instruments[] = $instruments;

        return $this;
    }

    /**
     * Remove instruments
     *
     * @param \ZE\BABundle\Entity\Instrument $instruments
     */
    public function removeInstrument(\ZE\BABundle\Entity\Instrument $instruments)
    {
        $this->instruments->removeElement($instruments);
    }

    /**
     * Get instruments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInstruments()
    {
        return $this->instruments;
    }



    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $addresses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $documents;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $genres;

    /**
     * @var \ZE\BABundle\Entity\User
     */
    protected $user;


    /**
     * Set name.

     *
     * @param string $name
     *
     * @return Musician
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

    /**
     * Set description.

     *
     * @param string $description
     *
     * @return Musician
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.

     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Add band.

     *
     * @param \ZE\BABundle\Entity\Band $band
     *
     * @return Musician
     */
    public function addBand(\ZE\BABundle\Entity\Band $band)
    {
        $this->bands[] = $band;

        return $this;
    }

    /**
     * Remove band.

     *
     * @param \ZE\BABundle\Entity\Band $band
     */
    public function removeBand(\ZE\BABundle\Entity\Band $band)
    {
        $this->bands->removeElement($band);
    }

    /**
     * Get bands.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBands()
    {
        return $this->bands;
    }

    /**
     * Add address.

     *
     * @param \ZE\BABundle\Entity\Address $address
     *
     * @return Musician
     */
    public function addAddress(\ZE\BABundle\Entity\Address $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address.

     *
     * @param \ZE\BABundle\Entity\Address $address
     */
    public function removeAddress(\ZE\BABundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add document.

     *
     * @param \ZE\BABundle\Entity\Document $document
     *
     * @return Musician
     */
    public function addDocument(\ZE\BABundle\Entity\Document $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document.

     *
     * @param \ZE\BABundle\Entity\Document $document
     */
    public function removeDocument(\ZE\BABundle\Entity\Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents.

     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add genre.

     *
     * @param \ZE\BABundle\Entity\Genre $genre
     *
     * @return Musician
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
     * Set user.

     *
     * @param \ZE\BABundle\Entity\User $user
     *
     * @return Musician
     */
    public function setUser(\ZE\BABundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.

     *
     * @return \ZE\BABundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
