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

/**
 * Form contact entity class.
 *
 * @ORM\Entity(repositoryClass="Zikula\FormiculaModule\Entity\Repository\ContactRepository")
 * @ORM\Table(name="formicula_contact")
 */
class ContactEntity extends EntityAccess
{
    /**
     * The contact id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    private $cid;

    /**
     * The contact name
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 100)
     * @ORM\Column(type="string", length=100)
     * @var string
     */
    private $name;

    /**
     * The email address
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 200)
     * @Assert\Email()
     * @ORM\Column(type="string", length=200)
     * @var string
     */
    private $email;

    /**
     * The public flag
     *
     * @ORM\Column(type="boolean")
     * @Assert\Type(type="bool")
     * @var boolean
     */
    private $public;

    /**
     * The sender name for replying
     *
     * @Assert\Length(max = 100)
     * @ORM\Column(type="string", length=100, nullable=true)
     * @var string
     */
    private $senderName;

    /**
     * The sender email address for replying
     *
     * @Assert\Length(max = 100)
     * @Assert\Email()
     * @ORM\Column(type="string", length=100, nullable=true)
     * @var string
     */
    private $senderEmail;

    /**
     * The subject for replying
     *
     * @Assert\Length(max = 150)
     * @ORM\Column(type="string", length=150, nullable=true)
     * @var string
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
     */
    public function getCid(): ?int
    {
        return $this->cid;
    }

    /**
     * Sets the id for the contact.
     */
    public function setCid(int $cid): void
    {
        $this->cid = $cid;
    }

    /**
     * Gets the name of the contact.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name for the contact.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the email address of the contact.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the email address for the contact.
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Gets the public flag of the contact.
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * Sets the public flag for the contact.
     */
    public function setPublic(bool $public): void
    {
        if (is_bool($public)) {
            $this->public = $public;
        }
    }

    /**
     * Gets the sender name of the contact.
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * Sets the sender name for the contact.
     */
    public function setSenderName(string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * Gets the sender email address of the contact.
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * Sets the sender email address for the contact.
     */
    public function setSenderEmail(string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * Gets the sending subject of the contact.
     */
    public function getSendingSubject(): string
    {
        return $this->sendingSubject;
    }

    /**
     * Sets the sending subject for the contact.
     */
    public function setSendingSubject(string $subject): void
    {
        $this->sendingSubject = $subject;
    }
}
