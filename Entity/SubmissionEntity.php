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
 * @ORM\Entity(repositoryClass="Zikula\FormiculaModule\Entity\Repository\SubmissionRepository")
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
     */
    public function getSid(): ?int
    {
        return $this->sid;
    }

    /**
     * Sets the id for the submission.
     */
    public function setSid(int $sid): void
    {
        $this->sid = $sid;
    }

    /**
     * Gets the id of the form.
     */
    public function getForm(): int
    {
        return $this->form;
    }

    /**
     * Sets the form id for the submission.
     */
    public function setForm(int $form): void
    {
        $this->form = $form;
    }

    /**
     * Gets the contact id of the submission.
     */
    public function getCid(): int
    {
        return $this->cid;
    }

    /**
     * Sets the contact id for the submission.
     */
    public function setCid(int $cid): void
    {
        $this->cid = $cid;
    }

    /**
     * Gets the ip address of the submission.
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * Sets the ip address for the submission.
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Gets the host name of the submission.
     */
    public function getHostName(): string
    {
        return $this->hostName;
    }

    /**
     * Sets the host name for the submission.
     */
    public function setHostName(string $hostName): void
    {
        $this->hostName = $hostName;
    }

    /**
     * Gets the name of the submission.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name for the submission.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the email address of the submission.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the email address for the submission.
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Gets the phone number of the submission.
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * Sets the phone number for the submission.
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Alias for easier form handling.
     */
    public function setPhone(string $phoneNumber): void
    {
        $this->setPhoneNumber($phoneNumber);
    }

    /**
     * Gets the company of the submission.
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * Sets the company for the submission.
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * Gets the url of the submission.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Sets the url for the submission.
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * Gets the location of the submission.
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Sets the location for the submission.
     */
    public function setLocation(string $location): void
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
     */
    public function setComment($comment): string
    {
        $this->comment = $comment;
    }

    /**
     * Gets the custom data of the submission.
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * Sets the custom data for the submission.
     */
    public function setCustomData(array $customData = []): void
    {
        $this->customData = $customData;
    }

    /**
     * Adds a value to the submission's custom data.
     */
    public function addCustomData(string $key = '', string $value = ''): void
    {
        if ('' !== $key) {
            $this->customData[$key] = $value;
        }
    }
}
