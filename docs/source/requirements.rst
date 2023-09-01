.. _requirements:

Requirements
============

PHP 5.5.9 or later is required.

* `MySQL`_ or `MariaDB`_.

PHP dependencies are handled with `Composer`_. Composer should be installed globally. The Symfony framework has additional requirements for PHP.

.. note:: Some php extensions may not be activated. One such extension that is used in DHIL Symfony projects is the `Internalization`_ extension. To enable it, find the php.ini file on your disk, open it, and remove the comment sign ";" in front of it.

Javascript and CSS dependencies are managed with `Bower`_, which requires
`NPM`_ and `NodeJS`_.

.. _Internalization: http://php.net/manual/en/book.intl.php
.. _MySQL: https://www.mysql.com/downloads/
.. _MariaDB: https://mariadb.org/
.. _Composer: https://getcomposer.org/
.. _Bower: https://bower.io/
.. _NPM: https://www.npmjs.com/
.. _NodeJS: https://nodejs.org/en/
