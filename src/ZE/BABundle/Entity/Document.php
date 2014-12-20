<?php

namespace ZE\BABundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Imagine\Gd;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
/**
 * @ORM\Entity
 * @ORM\Table(name="document")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="ZE\BABundle\Entity\Repository\Document")
 */
class Document
{
    const DOCUMENT_TYPE_IMAGE = 1;
    const UPLOAD_DIR = 'img/users';
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;
    /**
     * @ORM\Column(name="crop_params", type="string", length=255, nullable=true)
     */
    protected $cropParams;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    protected $isDefault;

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : self::UPLOAD_DIR.'/'.$this->path;
    }

    public static function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.self::UPLOAD_DIR;
    }



    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;

    private $temp;

    /**
     * Sets file.
     *
     * @param Document $file
     */
    public function setFile( $file = null)
    {
        $this->file = $file;
        $this->name = $file->getClientOriginalName();
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
//            $this->name = $this->getFile();
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * Get file.
     *
     * @return Document
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }
        if(!empty($this->cropParams)) {
            $path = self::getUploadRootDir() . '/' .$this->path;
            $arrCropParams = json_decode($this->cropParams,true);
            $imagine = new  Imagine();
            $image = $imagine->open($this->getFile());
            $image
                ->crop(new Point($arrCropParams['x1'], $arrCropParams['y1']),
                    new Box($arrCropParams['w'], $arrCropParams['h']))
                ->save($path);
        } else {
            // if there is an error when moving the file, an exception will
            // be automatically thrown by move(). This will properly prevent
            // the entity from being persisted to the database on error
            $this->getFile()->move(self::getUploadRootDir(), $this->path);
        }

        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    /**
     * Lifecycle callback to upload the file to the server
     */
    public function lifecycleFileUpload() {
        $this->upload();
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated() {
        $this->setUpdated(date('Y-m-d H:i:s'));
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
     * Set path
     *
     * @param string $path
     * @return Document
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function __toString()
    {
        return $this->getWebPath();
    }
    /**
     * @ORM\ManyToOne(targetEntity="Association", inversedBy="documents")
     **/

    protected $association;

    public function setAssociation(Association $association)
    {
        $this->association = $association;
    }

    public function getAssociation()
    {
        return $this->association;
    }


    /**
     * Set cropParams
     *
     * @param string $cropParams
     *
     * @return Document
     */
    public function setCropParams($cropParams)
    {
        $this->cropParams = $cropParams;

        return $this;
    }

    /**
     * @return string
     */
    public function getCropParams()
    {
        return $this->cropParams;
    }



    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Document
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return Document
     */
    public function setIsDefault($isDefault)
    {
        foreach($this->getAssociation()->getDocuments as $doc){
            $doc->setIsDefault(false);
        }
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }
}
