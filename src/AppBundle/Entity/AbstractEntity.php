<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
abstract class AbstractEntity
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"public", "private"})
     */
    protected $id;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Groups({"public", "private"})
     */
    protected $created;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Groups({"public", "private"})
     */
    protected $updated;

    public function getId() {
        return $this->id;
    }

    private function setCreated() {
        // nop
    }

    /**
     * @return DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    private function setUpdated() {
        // nop
    }

    /**
     * @return DateTime
     */
    public function getUpdated() {
        return $this->updated;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        $this->created = new DateTime();
        $this->updated = new DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate() {
        $this->updated = new DateTime();
    }

    abstract public function __toString();

}
