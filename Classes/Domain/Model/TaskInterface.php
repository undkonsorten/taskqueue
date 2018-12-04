<?php
declare(strict_types=1);
namespace Undkonsorten\Taskqueue\Domain\Model;

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
interface TaskInterface
{

    /**
     * The job is waiting to be executed
     */
    public const WAITING = 0;

    /**
     * The job is running
     */
    public const RUNNING = 1;

    /**
     * The job is finished
     */
    public const FINISHED = 2;

    /**
     * The job failed
     */
    public const FAILED = 3;

    /**
     *  The job needs to be rerun
     */
    public const RETRY = 4;

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName(): string;

    /**
     * Sets the name
     *
     * @param string $name
     */
    public function setName(string $name): void;

    /**
     * Returns the data
     *
     * @return array $data
     */
    public function getData(): ?array;

    /**
     * Sets the data
     *
     * @param array $data
     */
    public function setData(array $data): void;

    /**
     * Returns the status
     *
     * @return int $status
     */
    public function getStatus(): int;

    /**
     * Sets the status
     *
     * @param int $status
     */
    public function setStatus(int $status): void;

    /**
     * Returns the startDate
     *
     * @return int $startDate
     */
    public function getStartDate(): int;

    /**
     * Sets the startDate
     *
     * @param int $startDate
     */
    public function setStartDate(int $startDate): void;

    /**
     * Returns the message
     *
     * @return string $message
     */
    public function getMessage(): string;

    /**
     * Sets the message
     *
     * @param string $message
     */
    public function setMessage(string $message): void;

    /**
     * Returns the priority
     *
     * @return int $priority
     */
    public function getPriority(): int;

    /**
     * Sets the priority
     *
     * @param int $priority
     */
    public function setPriority(int $priority): void;

    /**
     * Runs the Task
     */
    public function run(): void;
}
