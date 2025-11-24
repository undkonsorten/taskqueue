.. include:: /Includes.rst.txt

.. _for-editors:

===========
For Editors
===========

After installation you should see a menu called **Task Queue**. Here all tasks are shown.

.. figure:: /Images/TaskQueue_Backend.png
   :alt: TaskQueue Backend

|

Tasks can have four different statuses:

- Red: Task failed.
- Yellow: Task is running.
- Blue: Task is waiting.
- Green: Task is finished.

A Task can be started and deleted manually.

Each task has a start date, which defines the date the task can be started. This is not respected when starting a task manually.

Message of tasks can contain log message but also exception.

Every task can have a priority.

You can not create tasks manually, because this would not make sense. A task can only be created by another extension, because you
have to implement the run() method. More about that see :ref:`dev-manual`.

Commands
--------

::

   ./vendor/bin/typo3cms taskqueue:run-tasks limit whitelist blacklist

Limit defines how many tasks should be executed.

Whitelist should be a comma seperated list of names. Every task name that is included in this list is executed.

Blacklist is analog the opposite.

These options exist for filtering task execution.

::

   ./vendor/bin/typo3cms taskqueue:delete-tasks dateInterval

DateInterval defines the intervall after witch the tasks should be deleted.
The following syntax is supported:
https://www.php.net/manual/en/dateinterval.construct.php

::

   ./vendor/bin/typo3cms taskqueue:activate-deferred-tasks dateInterval

All deferred tasks in this date intervall will be activated (status = WAITING)

::

   ./vendor/bin/typo3cms taskqueue:reactivate-failed-tasks dateInterval

All failed tasks in this date intervall will be reactivated (status = RETRY)


::

   ./vendor/bin/typo3cms taskqueue:notify-on-failure name count email interval status

With this command notification can be controlled.

- name: The name of the task to be watched.
- count: Number of failed tasks.
- email: Email to send the notification to.
- interval: Date interval of tasks that should be respected.
- status: Status of the task: 0|1|2|3|4|5|6 (default: 3)

Example:

Notify es@as.de when there are more than 10 failed tasks in the last three days with name Undkonsorten\Motion\Domain\Model\Task\MotionTask

::

   ./vendor/bin/typo3cms taskqueue:notify-on-failure --name='Undkonsorten\Motion\Domain\Model\Task\MotionTask' --count=10 --email='fonds@undkonsorten.com' --interval=P3D

Cronjob
-------
Task can also be started via cronjob.

::

   ./vendor/bin/typo3cms taskqueue:run-tasks 15 "MailTask,ApiTask"

This will start 15 tasks named "MailTask" or "ApiTask"

::

   ./vendor/bin/typo3cms taskqueue:delete-tasks P3M

This will delete tasks older than 3 month.


Scheduler
---------
It is possible to add this cronjob to the scheduler.

- Simply click on the add icon and choose **Execute Console Commands**.
