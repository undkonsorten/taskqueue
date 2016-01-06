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
abstract class Task extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity implements TaskInterface {

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
	 * @var integer
	 */
	protected $status = 0;

	/**
	 * startDate
	 *
	 * @var integer
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
	 * @var integer
	 */
	protected $priority = 0;

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the data
	 *
	 * @return array $data
	 */
	public function getData() {
		return unserialize($this->data);
	}

	/**
	 * Sets the data
	 *
	 * @param array $data
	 * @return void
	 */
	public function setData($data) {
		$this->data = serialize($data);
	}

	/**
	 * @TODO move to abstract class
	 *
	 * @param \string $property
	 * @param \mixed $value
	 * @return void
	 */
	protected function setProperty($property, $value) {
		if(is_scalar($value) || is_array($value)){
			$data = $this->getData();
			$data[$property] = $value;
			$this->setData($data);
		}else{
			throw new \Exception('Dont put complex types to a task, it might not be serialisable',1452100146);
		}
		
	}
	
	/**
	 * @TODO move to abstract class
	 *
	 * @param \string $property
	 * @return \mixed
	 */
	protected function getProperty($property) {
		$data = $this->getData();
		return $data[$property];
	}
	
	/**
	 * Returns the status
	 *
	 * @return integer $status
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Sets the status
	 *
	 * @param integer $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * Returns the startDate
	 *
	 * @return integer $startDate
	 */
	public function getStartDate() {
		return $this->startDate;
	}

	/**
	 * Sets the startDate
	 *
	 * @param integer $startDate
	 * @return void
	 */
	public function setStartDate($startDate) {
		$this->startDate = $startDate;
	}

	/**
	 * Returns the message
	 *
	 * @return string $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * Sets the message
	 *
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * Returns the priority
	 *
	 * @return integer $priority
	 */
	public function getPriority() {
		return $this->priority;
	}

	/**
	 * Sets the priority
	 *
	 * @param integer $priority
	 * @return void
	 */
	public function setPriority($priority) {
		$this->priority = $priority;
	}

}