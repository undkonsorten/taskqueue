<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Domain\Model;

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
abstract class Task extends AbstractEntity implements TaskInterface
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
     * Don't store serialized objects here, use UIDs instead
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
    public function setName($name): void
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
        return $data[$property];
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
    public function setStatus($status): void
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
    public function setStartDate($startDate): void
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
    public function setMessage($message): void
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
    public function setPriority($priority): void
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
}
