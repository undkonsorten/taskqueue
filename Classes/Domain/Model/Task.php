<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Domain\Model;

use JsonSerializable;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Eike Starkmann <starkmann@undkonsorten.com>, undkonsorten
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * A Task
 */
abstract class Task extends AbstractEntity implements TaskInterface, JsonSerializable
{

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * data
     *
     * Key-value json is expected here
     * Example: {'TaskClass': 1}
     *
     * @var string
     */
    protected $data = '';

    /**
     * status
     *
     * @var int
     */
    protected $status = 0;

    /**
     * startDate
     *
     * @var int
     */
    protected $startDate = 0;

    /**
     * @var \DateTime|null
     */
    protected $lastRun;

    /**
     * message
     *
     * @var string
     */
    protected $message = '';

    /**
     * priority
     *
     * @var int
     */
    protected $priority = 0;

    /**
     * @var int
     */
    protected $retries = 3;

    /**
     * time to live in seconds
     * @var int
     */
    protected int $ttl = 900;

    public function __construct()
    {
        $this->name = static::class;
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Override this function to have a different short name in lists
     *
     * @return string
     */
    public function getShortName(): string
    {
        return $this->getLastPartOfNamespacedClassName($this->getName());
    }

    /**
     * Override this to add custom information to the task list
     *
     * @return string
     */
    public function getAdditionalInformation(): string
    {
        return '';
    }

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the data
     *
     * @return array $data
     */
    public function getData(): ?array
    {
        /** @noinspection PhpComposerExtensionStubsInspection */
        return json_decode($this->data, true);
    }

    /**
     * Sets the data
     *
     * @param array $data
     */
    public function setData(array $data): void
    {
        /** @noinspection PhpComposerExtensionStubsInspection */
        $this->data = json_encode($data);
    }

    public function getAllData(): array
    {
        return array_merge($this->getData() ?? [], $this->getAdditionalData());
    }

    public function getAdditionalData(): array
    {
        return [];
    }

    /**
     * @param string $property
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    protected function setProperty($property, $value): void
    {
        if (is_array($value)) {
            array_walk_recursive($value, function ($element) {
                if (!is_array($element) && !self::isScalarOrNull($element)) {
                    throw new \InvalidArgumentException('The given array contains a complex type. Dont put complex types to a task, it might not be serializable.', 1452100147);
                }
            });

            $data = $this->getData();
            $data[$property] = $value;
            $this->setData($data);
        } elseif (self::isScalarOrNull($value)) {
            $data = $this->getData();
            $data[$property] = $value;
            $this->setData($data);
        } else {
            throw new \InvalidArgumentException('Dont put complex types to a task, it might not be serializable', 1452100146);
        }
    }

    protected static function isScalarOrNull($value): bool
    {
        return is_scalar($value) || $value === null;
    }

    /**
     * @param string $property
     * @return mixed
     */
    protected function getProperty($property)
    {
        $data = $this->getData();
        return $data[$property] ?? null;
    }

    /**
     * Returns the status
     *
     * @return int $status
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Returns the startDate
     *
     * @return int $startDate
     */
    public function getStartDate(): int
    {
        return $this->startDate;
    }

    /**
     * Sets the startDate
     *
     * @param int $startDate
     */
    public function setStartDate(int $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * Returns the message
     *
     * @return string $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Sets the message
     *
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Returns the priority
     *
     * @return int $priority
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Sets the priority
     *
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return int
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * @param int $retries
     */
    public function setRetries(int $retries): void
    {
        $this->retries = $retries;
    }

    protected function getLastPartOfNamespacedClassName(string $className): string
    {
        $nameParts = explode('\\', $className);
        return array_pop($nameParts);
    }

    /**
     * Override this function and let it return an array
     * <pre>
     * [
     *   "tableName" => "tx_myext_table",
     *   "uid" => "34",
     * ]
     * </pre>
     * to create a backend link to the record in the single view of a task.
     *
     * @return array|null
     */
    public function getRecord(): ?array
    {
        return null;
    }

    /**
     * Override this function to modify the linked text of a record link. The link
     * text will be "tx_myext_table:34" otherwise
     *
     * @see \Undkonsorten\Taskqueue\Domain\Model\Task::getRecord
     * @return string|null
     */
    public function getRecordLabel(): ?string
    {
        return null;
    }

    public function markRunning(): void
    {
        $this->setStatus(TaskInterface::RUNNING);
        $this->setLastRun(new \DateTime('now'));
    }

    public function markFinished(): void
    {
        $this->setStatus(TaskInterface::FINISHED);
    }

    public function markFailed(): void
    {
        $this->setStatus(TaskInterface::FAILED);
    }

    public function markRetry(): void
    {
        $this->setStatus(TaskInterface::RETRY);
    }

    /**
     * Reactivates a failed task
     *
     * @param int $retries
     * @return mixed|void
     */
    public function reactivate(int $retries = 3)
    {
        $this->setStatus(TaskInterface::RETRY);
        $this->setRetries($retries);
    }

    /**
     * @return \DateTime|null
     */
    public function getLastRun(): ?\DateTime
    {
        return $this->lastRun;
    }

    /**
     * @param \DateTime|null $lastRun
     */
    public function setLastRun(?\DateTime $lastRun): void
    {
        $this->lastRun = $lastRun;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'date' => $this->data,
            'status' => $this->status,
            'startDate' => $this->startDate,
            'lastRun' => $this->lastRun,
            'message' => $this->message,
            'priority' => $this->priority,
            'retries' => $this->retries,
            'ttl' => $this->ttl,
        ];
    }

}
