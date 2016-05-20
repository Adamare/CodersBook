<?php

namespace CodersLab\CodersBookBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CLGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CodersLab\CodersBookBundle\Entity\CLGroupRepository")
 */
class CLGroup
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
     * @ORM\Column(name="name", type="string", length=15)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lecturer", type="string", length=100)
     */
    private $lecturer;


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
     * @return CLGroup
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
     * Set lecturer
     *
     * @param string $lecturer
     * @return CLGroup
     */
    public function setLecturer($lecturer)
    {
        $this->lecturer = $lecturer;

        return $this;
    }

    /**
     * Get lecturer
     *
     * @return string 
     */
    public function getLecturer()
    {
        return $this->lecturer;
    }
}
