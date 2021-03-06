<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *   itemOperations={
 *     "get",
 *     "put",
 *     "delete",
 *     "sort"={
 *        "method"="PUT",
 *        "path"="/tasks/{id}/sort.{_format}",
 *        "controller"="App\Action\Api\Tasks\Sort",
 *        "swagger_context"={"summary"="Sorts a task"},
 *        "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *       },
 *     },
 *   collectionOperations={
 *     "get", "post",
 *     "stats"={
 *       "method"="GET",
 *       "path"="/tasks/stats.{_format}",
 *       "controller"="App\Action\Api\Tasks\Stats",
 *       "swagger_context"={"summary"="Get stats for tasks"},
 *       "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *       "read"=false
 *     }
 *   },
 *   attributes={"order"={"order": "ASC"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={"project.id": "exact", "assigned.id": "exact", "status": "exact", "labels.id": "exact"})
 * @ApiFilter(OrderFilter::class, properties={"status"}, arguments={"orderParameterName"="order"})
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $project;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Label", inversedBy="tasks", cascade={"persist"})
     */
    private $labels;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $assigned;

    /**
     * @ORM\Column(type="integer", nullable=true, name="`order`")
     */
    private $order;

    public function __construct()
    {
        $this->labels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection|Label[]
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }

    public function addLabel(Label $label): self
    {
        if (!$this->labels->contains($label)) {
            $this->labels[] = $label;
            $label->addTask($this);
        }

        return $this;
    }

    public function removeLabel(Label $label): self
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
            $label->removeTask($this);
        }

        return $this;
    }

    public function getAssigned(): ?User
    {
        return $this->assigned;
    }

    public function setAssigned(?User $assigned): self
    {
        $this->assigned = $assigned;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): self
    {
        $this->order = $order;

        return $this;
    }
}
