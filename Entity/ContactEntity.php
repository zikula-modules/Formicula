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
 * Form contact entity class.
 *
 * @ORM\Entity(repositoryClass="Zikula\FormiculaModule\Entity\Repository\ContactRepository")
 * @ORM\Table(name="formcontacts")
 */
class ContactEntity extends EntityAccess
{
    /**
     * The contact id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer $cid
     */
    private $cid;

    /**
     * The contact name
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 40)
     * @ORM\Column(type="string", length=40)
     * @var string $name
     */
    private $name;

    /**
     * The email address
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 80)
     * @Assert\Email()
     * @ORM\Column(type="string", length=80)
     * @var string $email
     */
    private $email;

    /**
     * The public flag
     *
     * @ORM\Column(type="boolean")
     * @Assert\Type(type="bool")
     * @var boolean $public
     */
    private $public;

    /**
     * The sender name for replying
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 80)
     * @ORM\Column(name="sname", type="string", length=80)
     * @var string $senderName
     */
    private $senderName;

    /**
     * The sender email address for replying
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 80)
     * @Assert\Email()
     * @ORM\Column(name="semail", type="string", length=80)
     * @var string $senderEmail
     */
    private $senderEmail;

    /**
     * The subject for replying
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 80)
     * @ORM\Column(name="ssubject", type="string", length=80)
     * @var string $sendingSubject
     */
    private $sendingSubject;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->name = '';
        $this->email = '';
        $this->public = false;
        $this->senderName = '';
        $this->senderEmail = '';
        $this->sendingSubject = '';
    }

    /**
     * Gets the id of the contact.
     *
     * @return integer the contact's id
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * Sets the id for the contact.
     *
     * @param integer $cid the contact's id
     */
    public function setCid($cid)
    {
        $this->cid = $cid;
    }

    /**
     * Gets the name of the contact.
     *
     * @return string the contact's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name for the contact.
     *
     * @param string $name the contact's name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the email address of the contact.
     *
     * @return string the contact's email address
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email address for the contact.
     *
     * @param string $email the contact's email address
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Gets the public flag of the contact.
     *
     * @return boolean the contact's public flag
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * Sets the public flag for the contact.
     *
     * @param boolean $public the contact's public flag
     */
    public function setPublic($public)
    {
        if (is_bool($public)) {
            $this->public = $public;
        }
    }

    /**
     * Gets the sender name of the contact.
     *
     * @return string the contact's sender name
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Sets the sender name for the contact.
     *
     * @param string $senderName the contact's sender name
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * Gets the sender email address of the contact.
     *
     * @return string the contact's sender email address
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Sets the sender email address for the contact.
     *
     * @param string $senderEmail the contact's sender email address
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * Gets the sending subject of the contact.
     *
     * @return string the contact's sending subject
     */
    public function getSendingSubject()
    {
        return $this->sendingSubject;
    }

    /**
     * Sets the sending subject for the contact.
     *
     * @param string $subject the contact's sending subject
     */
    public function setSendingSubject($subject)
    {
        $this->sendingSubject = $subject;
    }
}
