<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private ?string $phone_number = null;

    #[ORM\Column(length: 7)]
    private ?string $country_code = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'user_sent', targetEntity: Message::class)]
    #[Ignore]
    private Collection $messages;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'users')]
    #[Ignore]
    private Collection $groups;

    #[ORM\OneToMany(mappedBy: 'recipient_user', targetEntity: Recipient::class)]
    #[Ignore]
    private Collection $recipients;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->recipients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }

    public function setCountryCode(string $country_code): self
    {
        $this->country_code = $country_code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUserSent($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUserSent() === $this) {
                $message->setUserSent(null);
            }
        }

        return $this;
    }
    
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'phone_number' => $this->getPhoneNumber(),
            'country_code' => $this->getCountryCode(),
            'name' => $this->getName()
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new NotBlank());
        $metadata->addPropertyConstraint('phone_number', new NotBlank());
        $metadata->addPropertyConstraint('country_code', new NotBlank());
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addUser($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            $group->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Recipient>
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(Recipient $recipient): self
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
            $recipient->setRecipientUser($this);
        }

        return $this;
    }

    public function removeRecipient(Recipient $recipient): self
    {
        if ($this->recipients->removeElement($recipient)) {
            // set the owning side to null (unless already changed)
            if ($recipient->getRecipientUser() === $this) {
                $recipient->setRecipientUser(null);
            }
        }

        return $this;
    }
}
