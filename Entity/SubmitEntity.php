<?php

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Core\Doctrine\EntityAccess;

/**
 * Form submit entity class.
 *
 * @ORM\Entity(repositoryClass="Zikula\FormiculaModule\Entity\Repository\SubmitRepository")
 * @ORM\Table(name="formsubmits")
 */
class SubmitEntity extends EntityAccess
{
    /**
     * The submit id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer $sid
     */
    private $sid;

    /**
     * The form id
     *
     * @ORM\Column(type="integer")
     * @var integer $form
     */
    private $form;

    /**
     * The contact id
     *
     * @ORM\Column(type="integer")
     * @var integer $cid
     */
    private $cid;

    /**
     * The ip address
     *
     * @Assert\NotBlank()
     * @Assert\Ip()
     * @ORM\Column(name="ip", type="string", length=255)
     * @var string $ipAddress
     */
    private $ipAddress;

    /**
     * The host
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     * @var string $host
     */
    private $host;

    /**
     * The name
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     * @var string $name
     */
    private $name;

    /**
     * The email address
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", length=255)
     * @var string $email
     */
    private $email;

    /**
     * The phone number
     *
     * @ORM\Column(name="phone", type="string", length=255)
     * @var string $phoneNumber
     */
    private $phoneNumber;

    /**
     * The url
     *
     * @Assert\Url()
     * @ORM\Column(type="string", length=255)
     * @var string $url
     */
    private $url;

    /**
     * The location
     *
     * @ORM\Column(type="string", length=255)
     * @var string $location
     */
    private $location;

    /**
     * The comment
     *
     * @ORM\Column(type="text")
     * @var string $comment
     */
    private $comment;

    /**
     * Custom data array
     *
     * @ORM\Column(type="array")
     * @var array $customData
     */
    private $customData;

    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="create")
     * @Assert\Type(type="integer")
     * @var integer $createdUserId
     */
    protected $createdUserId;

    /**
     * @ORM\Column(type="integer")
     * @ZK\StandardFields(type="userid", on="update")
     * @Assert\Type(type="integer")
     * @var integer $updatedUserId
     */
    protected $updatedUserId;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Assert\DateTime()
     * @var \DateTime $createdDate
     */
    protected $createdDate;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Assert\DateTime()
     * @var \DateTime $updatedDate
     */
    protected $updatedDate;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->form = 0;
        $this->cid = 0;
        $this->ipAddress = '';
        $this->host = '';
        $this->name = '';
        $this->email = '';
        $this->phoneNumber = '';
        $this->url = '';
        $this->location = '';
        $this->comment = '';
        $this->customData = [];
    }

    /**
     * Gets the id of the submit.
     *
     * @return integer the submit's id
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Sets the id for the submit.
     *
     * @param integer $sid the submit's id
     */
    public function setSid($sid)
    {
        $this->sid = $sid;
    }

    /**
     * Gets the id of the form.
     *
     * @return integer the submit's form id
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Sets the form id for the submit.
     *
     * @param integer $form the submit's form id
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * Gets the contact id of the submit.
     *
     * @return integer the submit's contact id
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * Sets the contact id for the submit.
     *
     * @param integer $sid the submit's contact id
     */
    public function setCid($cid)
    {
        $this->cid = $cid;
    }

    /**
     * Gets the ip address of the submit.
     *
     * @return string the submit's ip address
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Sets the ip address for the submit.
     *
     * @param string $ipAddress the submit's ip address
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Gets the host of the submit.
     *
     * @return string the submit's host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the host for the submit.
     *
     * @param string $host the submit's host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Gets the name of the submit.
     *
     * @return string the submit's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name for the submit.
     *
     * @param string $name the submit's name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the email address of the submit.
     *
     * @return string the submit's email address
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email address for the submit.
     *
     * @param string $email the submit's email address
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Gets the phone number of the submit.
     *
     * @return string the submit's phone number
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Sets the phone number for the submit.
     *
     * @param string $phoneNumber the submit's phone number
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Gets the url of the submit.
     *
     * @return string the submit's url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url for the submit.
     *
     * @param string $url the submit's url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Gets the location of the submit.
     *
     * @return string the submit's location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets the location for the submit.
     *
     * @param string $location the submit's location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Gets the comment of the submit.
     *
     * @return string the submit's comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment for the submit.
     *
     * @param string $comment the submit's comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Gets the custom data of the submit.
     *
     * @return string the submit's custom data
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * Sets the custom data for the submit.
     *
     * @param string $customData the submit's custom data
     */
    public function setCustomData($customData)
    {
        $this->customData = $customData;
    }

    /**
     * Adds a value to the submit's custom data.
     *
     * @param string $key   name of the custom property
     * @param string $value the property value
     */
    public function addCustomData($key, $value)
    {
        if ($key != '') {
            $this->customData[$key] = $value;
        }
    }

    /**
     * Gets the created user id.
     *
     * @return integer
     */
    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }

    /**
     * Sets the created user id.
     *
     * @param integer $createdUserId
     *
     * @return void
     */
    public function setCreatedUserId($createdUserId)
    {
        $this->createdUserId = $createdUserId;
    }

    /**
     * Gets the updated user id.
     *
     * @return integer
     */
    public function getUpdatedUserId()
    {
        return $this->updatedUserId;
    }

    /**
     * Sets the updated user id.
     *
     * @param integer $updatedUserId
     *
     * @return void
     */
    public function setUpdatedUserId($updatedUserId)
    {
        $this->updatedUserId = $updatedUserId;
    }

    /**
     * Gets the created date.
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Sets the created date.
     *
     * @param \DateTime $createdDate
     *
     * @return void
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * Gets the updated date.
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Sets the updated date.
     *
     * @param \DateTime $updatedDate
     *
     * @return void
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }
}
