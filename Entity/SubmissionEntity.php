<?php

declare(strict_types=1);

/*
 * This file is part of the Formicula package.
 *
 * Copyright Formicula Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\FormiculaModule\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\FormiculaModule\Traits\StandardFieldsTrait;

/**
 * Form submission entity class.
 *
 * @ORM\Entity(repositoryClass="SubmissionRepository")
 * @ORM\Table(name="formicula_submission")
 */
class SubmissionEntity extends EntityAccess
{
    use StandardFieldsTrait;

    /**
     * The submission id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    private $sid;

    /**
     * The form id
     *
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $form;

    /**
     * The contact id
     *
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $cid;

    /**
     * The ip address
     *
     * @Assert\NotBlank()
     * @Assert\Ip()
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $ipAddress;

    /**
     * The host name
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $hostName;

    /**
     * The name
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * The email address
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $email;

    /**
     * The phone number
     *
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $phoneNumber;

    /**
     * The company
     *
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $company;

    /**
     * The url
     *
     * @Assert\Url()
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $url;

    /**
     * The location
     *
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $location;

    /**
     * The comment
     *
     * @ORM\Column(type="text")
     * @var string
     */
    private $comment;

    /**
     * Custom data array
     *
     * @ORM\Column(type="array")
     * @var array
     */
    private $customData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->form = 0;
        $this->cid = 0;
        $this->ipAddress = '';
        $this->hostName = '';
        $this->name = '';
        $this->email = '';
        $this->phoneNumber = '';
        $this->company = '';
        $this->url = '';
        $this->location = '';
        $this->comment = '';
        $this->customData = [];
    }

    /**
     * Gets the id of the submission.
     *
     * @return integer the submission's id
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Sets the id for the submission.
     *
     * @param integer $sid the submission's id
     */
    public function setSid($sid)
    {
        $this->sid = $sid;
    }

    /**
     * Gets the id of the form.
     *
     * @return integer the submission's form id
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Sets the form id for the submission.
     *
     * @param integer $form the submission's form id
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * Gets the contact id of the submission.
     *
     * @return integer the submission's contact id
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * Sets the contact id for the submission.
     *
     * @param integer $sid the submission's contact id
     */
    public function setCid($cid)
    {
        $this->cid = $cid;
    }

    /**
     * Gets the ip address of the submission.
     *
     * @return string the submission's ip address
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Sets the ip address for the submission.
     *
     * @param string $ipAddress the submission's ip address
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Gets the host name of the submission.
     *
     * @return string the submission's host name
     */
    public function getHostName()
    {
        return $this->hostName;
    }

    /**
     * Sets the host name for the submission.
     *
     * @param string $hostName the submission's host name
     */
    public function setHostName($hostName)
    {
        $this->hostName = $hostName;
    }

    /**
     * Gets the name of the submission.
     *
     * @return string the submission's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name for the submission.
     *
     * @param string $name the submission's name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the email address of the submission.
     *
     * @return string the submission's email address
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email address for the submission.
     *
     * @param string $email the submission's email address
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Gets the phone number of the submission.
     *
     * @return string the submission's phone number
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Sets the phone number for the submission.
     *
     * @param string $phoneNumber the submission's phone number
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Alias for easier form handling.
     *
     * @param string $phoneNumber the submission's phone number
     */
    public function setPhone($phoneNumber)
    {
        return $this->setPhoneNumber($phoneNumber);
    }

    /**
     * Gets the company of the submission.
     *
     * @return string the submission's company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Sets the company for the submission.
     *
     * @param string $company the submission's company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Gets the url of the submission.
     *
     * @return string the submission's url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the url for the submission.
     *
     * @param string $url the submission's url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Gets the location of the submission.
     *
     * @return string the submission's location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets the location for the submission.
     *
     * @param string $location the submission's location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Gets the comment of the submission.
     *
     * @return string the submission's comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment for the submission.
     *
     * @param string $comment the submission's comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Gets the custom data of the submission.
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * Sets the custom data for the submission.
     *
     * @param string $customData the submission's custom data
     */
    public function setCustomData($customData)
    {
        $this->customData = $customData;
    }

    /**
     * Adds a value to the submission's custom data.
     *
     * @param string $key   name of the custom property
     * @param string $value the property value
     */
    public function addCustomData($key, $value)
    {
        if ('' !== $key) {
            $this->customData[$key] = $value;
        }
    }
}
