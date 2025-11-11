.. include:: /Includes.rst.txt

Extension settings
=========

Activate TTL
------------------

.. confval:: activateTTL

   :type: boolean
   :Default: false

   With this feature switch you can activate the ttl (time to live) feature. The default ttl is 900 seconds.
   All task running longer then 900 seconds will be marked as failed.
