.. _testing:

Testing
=======

DHIL Symfony applications use `PHPUnit Testing Framework`_ to run automated tests on the code components. PHPUnit manual is `here <https://phpunit.de/manual/6.3/en/index.html>`_. A comprehensive tutorial can be found `here <https://jtreminio.com/2013/03/>`_.

.. note:: PHPUnit is enabled after installing Composer and CEWW specific composer dependencies. It should be located at ceww/vendor/bin/phpunit. You can check the installation guide `here <https://phpunit.de/manual/current/en/installation.html>`_ for more details. 

1. Navigate into the ceww main directory. Check to see if the phpunit is active:

.. code-block:: bash

  vendor/bin/phpunit --version

2. In order to run a particular test class, type: 

.. code-block:: bash

  vendor/bin/phpunit <path_of_the_file>

Alternatively, you can run the entire test suite by omitting the file path:

.. code-block:: bash

  vendor/bin/phpunit 

.. note:: Running the entire test suite can take a lot of time. You can fasten this process by replacing the "phpunit.xml.dist" file with the "phpunit.xml" (simply remove the extension). You can then tweak the `configuration file <https://phpunit.de/manual/current/en/appendixes.configuration.html>`_ to suit your needs. 

3. It would be helpful to get some sort of report on our tests. PHPUnit comes with a code coverage analysis tool. This tool needs the `Xdebug`_ extension for PHP to be enabled.

If you are on Windows, get the extension from here. Place the file into your php/ext/ folder. (if you use a server suite like XAMPP, php folder should be in it). If you are on MAC OS, type the following in terminal:

.. code-block:: bash

  brew install homebrew/php/<php-version>-xdebug

To enable the extension, add the following line to the php.ini file:

zend_extension="file_path"

More information can be found in `Xdebug Installation Guide`_.

4. Time to get our coverage report. This will likely take some time:

.. code-block:: bash

  vendor/bin/phpunit --coverage-html coverage

More information on coverage reports can be found `here <https://phpunit.de/manual/current/en/code-coverage-analysis.html>`_.

.. note:: since this step generates a folder named "coverage" in the main application directory, we need to add its path into our "gitignore" file (located in the main app directory) to prevent it from being pushed to the main repository. 


.. _`PHPUnit Testing Framework`: https://phpunit.de/
.. _`Xdebug`: https://xdebug.org/
.. _`Xdebug Installation Guide`: https://xdebug.org/docs/install