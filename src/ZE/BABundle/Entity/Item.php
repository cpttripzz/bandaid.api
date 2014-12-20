<?php

namespace ZE\BABundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity
 */
class Item
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
     * @ORM\Column(name="fs_id", type="string", length=255)
     */
    private $fsId;


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
     * Set fsId
     *
     * @param string $fsId
     *
     * @return Item
     */
    public function setFsId($fsId)
    {
        $this->fsId = $fsId;

        return $this;
    }

    /**
     * Get fsId
     *
     * @return string
     */
    public function getFsId()
    {
        return $this->fsId;
    }
}
