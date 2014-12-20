<?php
/**
 * Created by PhpStorm.
 * User: z
 * Date: 22/03/14
 * Time: 19:46
 */

namespace ZE\BABundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;


/**
    * An item a user can have a many to many relationship with, ie: Association, artist
    *
    * @ORM\Entity
    * @ORM\Table(name="association")
    * @ORM\InheritanceType("SINGLE_TABLE")
    * @ORM\DiscriminatorColumn(name="type", type="string")
    * @ORM\DiscriminatorMap({"association" = "Association", "musician" = "Musician", "band" = "Band"})
    * @ORM\Entity(repositoryClass="ZE\BABundle\Entity\Repository\Association")
    */
class Association
{

    use TimestampableEntity;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",  nullable=false)
     */
    protected $name;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="Address", inversedBy="associations")
     * @ORM\JoinTable(name="association_address",
     *   joinColumns={@ORM\JoinColumn(name="association_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="address_id", referencedColumnName="id")}
     * )
     */
    protected $addresses;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="association", cascade={"persist"})
     */
    protected $documents;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="associations")
     * @ORM\JoinTable(name="association_genre",
     *   joinColumns={@ORM\JoinColumn(name="association_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="genre_id", referencedColumnName="id")}
     * )
     */
    protected $genres;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="associations",cascade={"persist"})
     */
    protected $user;

    /**
     * @var integer $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\Slug(fields={"name", "id"})
     * @ORM\Column(unique=true)
     */
    private $slug;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->name;
    }


    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }



    /**
     * Add documents
     *
     * @param \ZE\BABundle\Entity\Document $documents
     * @return Association
     */
    public function addDocument(\ZE\BABundle\Entity\Document $documents)
    {
        $this->documents[] = $documents;

        return $this;
    }

    /**
     * Remove documents
     *
     * @param \ZE\BABundle\Entity\Document $documents
     */
    public function removeDocument(\ZE\BABundle\Entity\Document $documents)
    {
        $this->documents->removeElement($documents);
    }

    /**
     * Get addresss
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add addresss
     *
     * @param \ZE\BABundle\Entity\Address $addresss
     * @return Association
     */
    public function addAddress(\ZE\BABundle\Entity\Address $addresss)
    {
        $this->addresses[] = $addresss;

        return $this;
    }

    /**
     * Remove addresss
     *
     * @param \ZE\BABundle\Entity\Address $addresss
     */
    public function removeAddress(\ZE\BABundle\Entity\Address $addresss)
    {
        $this->addresses->removeElement($addresss);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }
    /**
     * Add genres
     *
     * @param \ZE\BABundle\Entity\Genre $genres
     * @return Association
     */
    public function addGenre(\ZE\BABundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;

        return $this;
    }

    /**
     * Remove genres
     *
     * @param \ZE\BABundle\Entity\Genre $genres
     */
    public function removeGenre(\ZE\BABundle\Entity\Genre $genres)
    {
        $this->genres->removeElement($genres);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Association
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

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Association
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getType()
    {
        return get_class($this);
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Association
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDocumentsByTypeId($typeId)
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('type', $typeId));

        return $this->documents->matching($criteria);
    }

    public function getClassName()
    {
        $class = explode('\\', get_class($this));
        return strtolower( array_pop($class) );

    }
}
