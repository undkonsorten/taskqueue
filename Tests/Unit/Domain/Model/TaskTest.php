<?php

namespace Undkonsorten\Taskqueue\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Eike Starkmann <starkmann@undkonsorten.com>, undkonsorten
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \Undkonsorten\Taskqueue\Domain\Model\Task.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Eike Starkmann <starkmann@undkonsorten.com>
 */
class TaskTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Undkonsorten\Taskqueue\Domain\Model\Task
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Undkonsorten\Taskqueue\Domain\Model\Task();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getNameReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getName()
		);
	}

	/**
	 * @test
	 */
	public function setNameForStringSetsName() {
		$this->subject->setName('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'name',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDataReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getData()
		);
	}

	/**
	 * @test
	 */
	public function setDataForIntegerSetsData() {
		$this->subject->setData(12);

		$this->assertAttributeEquals(
			12,
			'data',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getStatusReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getStatus()
		);
	}

	/**
	 * @test
	 */
	public function setStatusForIntegerSetsStatus() {
		$this->subject->setStatus(12);

		$this->assertAttributeEquals(
			12,
			'status',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getStartDateReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getStartDate()
		);
	}

	/**
	 * @test
	 */
	public function setStartDateForIntegerSetsStartDate() {
		$this->subject->setStartDate(12);

		$this->assertAttributeEquals(
			12,
			'startDate',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getMessageReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getMessage()
		);
	}

	/**
	 * @test
	 */
	public function setMessageForStringSetsMessage() {
		$this->subject->setMessage('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'message',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPriorityReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getPriority()
		);
	}

	/**
	 * @test
	 */
	public function setPriorityForIntegerSetsPriority() {
		$this->subject->setPriority(12);

		$this->assertAttributeEquals(
			12,
			'priority',
			$this->subject
		);
	}
}
