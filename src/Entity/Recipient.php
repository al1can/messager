<?php

namespace App\Entity;

use App\Repository\RecipientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: RecipientRepository::class)]
class Recipient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'recipients')]
    #[Ignore]
    private ?Message $message = null;

    #[ORM\ManyToOne(inversedBy: 'recipients')]
    private ?User $recipient_user = null;

    #[ORM\ManyToOne(inversedBy: 'recipients')]
    private ?Group $recipient_group = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getRecipientUser(): ?User
    {
        return $this->recipient_user;
    }

    public function setRecipientUser(?User $recipient_user): self
    {
        $this->recipient_user = $recipient_user;

        return $this;
    }

    public function getRecipientGroup(): ?Group
    {
        return $this->recipient_group;
    }

    public function setRecipientGroup(?Group $recipient_group): self
    {
        $this->recipient_group = $recipient_group;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            //'message' => $this->message->toArray(),
            'recipient user' => $this->recipient_user->toArray(),
            'recipient group' => $this->recipient_group === null ? null : $this->recipient_group->toArray()
        ];
    }
}
