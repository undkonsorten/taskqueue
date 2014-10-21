.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _admin-manual:

Administrator Manual
====================

Backend
-------

- Install the extension via extension manager. 

- Include the static template.

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


Cronjob
-------
Task can also be started via cronjob. 

::

   php_cli cli_dispatch.phpsh extbase taskqueue:runtasks --limit=5

This will start 5 tasks.


Scheduler
---------
It is possible to add this cronjob to the scheduler.

- Simply click on the add icon and choose **Extbase CommandController Task**.
- In CommandController Command choose **Taskqueue Taskqueue: runTasks**.
- After saving you can also specific a limit, which defines how many task can be executed in one run.

The other options should be selfexplaining.
