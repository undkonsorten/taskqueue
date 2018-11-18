<?php
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
interface TaskInterface {

	/**
	 * The job is waiting to be executed
	 */
	const WAITING = 0;

	/**
	 * The job is running
	 */
	const RUNNING = 1;

	/**
	 * The job is finished
	 */
	const FINISHED = 2;

	/**
	 * The job failed
	 */
	const FAILED = 3;

    /**
     *  The job needs to be rerun
     */
	const RETRY = 4;

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName();

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name);

	/**
	 * Returns the data
	 *
	 * @return string $data
	 */
	public function getData();

	/**
	 * Sets the data
	 *
	 * @param string $data
	 * @return void
	 */
	public function setData($data);

	/**
	 * Returns the status
	 *
	 * @return integer $status
	 */
	public function getStatus();

	/**
	 * Sets the status
	 *
	 * @param integer $status
	 * @return void
	 */
	public function setStatus($status);

	/**
	 * Returns the startDate
	 *
	 * @return integer $startDate
	 */
	public function getStartDate();

	/**
	 * Sets the startDate
	 *
	 * @param integer $startDate
	 * @return void
	 */
	public function setStartDate($startDate);

	/**
	 * Returns the message
	 *
	 * @return string $message
	 */
	public function getMessage();

	/**
	 * Sets the message
	 *
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message);

	/**
	 * Returns the priority
	 *
	 * @return integer $priority
	 */
	public function getPriority();

	/**
	 * Sets the priority
	 *
	 * @param integer $priority
	 * @return void
	 */
	public function setPriority($priority);

	/**
	 * Runs the Task
	 */
	public function run();

}
