.. include:: /Includes.rst.txt

.. _for-editors:

===========
For Editors
===========

After installation you should see a menu called **Task Queue**. Here all tasks are shown.

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
