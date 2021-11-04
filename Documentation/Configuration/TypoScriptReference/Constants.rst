.. include:: /Includes.rst.txt
.. highlight:: typoscript

.. index::
   TypoScript; Constants
.. _configuration-typoscript-constants:

Constants
=========

Configure page ids
------------------

.. confval:: storagePid

   :type: int
   :Default: 0

   The id of the page storing the tasks


   Example::

       module.tx_taskqueue.persistence {
         storagePid = 42
      }
