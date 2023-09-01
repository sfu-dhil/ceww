.. _install:

Installation
============

.. note::

    CEWW doesn't use labeled or numbered releases. The code in the master branch of the repository should be runnable.

Make sure the requirements are satisfied.

The CEWW application is based on Symfony 4.4. Installation follows the normal process for installing a Symfony application.

1. Get the code from GitHub. 

.. code-block:: bash

  git clone https://github.com/sfu-dhil/ceww.git

2. Get the submodules from Git. There is quite a bit of reusable code in the application, and it's organized with git submodules. Navigate into the 'ceww' directory and type:

.. code-block:: bash

  git submodule init
  git submodule update --recursive --remote

3. Create a database and database user. 

.. note:: On Windows you can use a server suite like `XAMPP`_ or `WAMP`_ -make sure you get the right PHP version.
  
.. code-block:: sql

  create database ceww;
  create user if not exists ceww@localhost;
  grant all on ceww.* to ceww@localhost;
  set password for ceww@localhost = password('hotpockets');

.. note:: Composer should ask for these automatically when installing the dependencies. In case if it doesn't, you can later pass the database parameters to the 'parameters.yml' file under the 'ceww/app/config/' manually.

4. `Install Composer`_ if it isn't already installed somewhere. Make sure you do a global installation.
  
5. Install the Composer dependencies (make sure the working directory is 'ceww'). Composer will ask for some configuration variables during installation.
  
.. code-block:: bash

  composer install --no-dev -o
   
Sometimes Composer runs out of memory. If that happens, try this alternate.
  
.. code-block:: bash

  php -d memory_limit=-1 `which composer` install --no-dev -o

6. Update file permissions if needed. The user running the web server must be able to write to `var/cache/*` and `var/logs/*` and `var/sessions/*`. The symfony docs provide `recommended commands`_ depending on your OS.

7. Please follow the instructions in the config.rst file to set up the configuration settings for this project.
  
8. Load the schema into the database. This is done with the Symfony console.
  
.. code-block:: bash

  ./bin/console doctrine:schema:update --force
  
8. Create an application user with full admin privileges. This is also done with the Symfony console.
  
.. code-block:: bash

  ./bin/console nines:create:user
  

9. If you haven't installed npm and yarn globally, you will have to install them. You could do this by running the below commands in the terminal.
  
.. code-block:: bash

  sudo apt install npm
  sudo npm install --global yarn

10. If you have installed npm and yarn globally, then set up yarn for this project by running the below command inside project directory.
  
.. code-block:: bash

  yarn install

11. Configure the web server. The application's `public/` directory must
    be accessible to the world. Symfony provides `example
    configurations`_ for most server setups.

12. The documentation module should be built seperately. You need the Sphinx to be already installed. Check the `DHIL Documentation Guide`_ for more information. 

Navigate to the 'ceww/docs' directory in the command line and type: 

.. code-block:: bash

  make html

12. Start the Symfony server by using the below command and navigate to the link displayed.
  
.. code-block:: bash

  symfony server:start

At this point, the web interface should be up and running, and you should
be able to login by following the Login link in the top right menu bar.

13. Once everything is done, you should stop the Symfomny server. Before you close the terminal, make sure to stop the server using this command.
  
.. code-block:: bash

  symfony server:stop

That should be it.

.. _`XAMPP`: https://www.apachefriends.org/download.html

.. _`WAMP`: http://www.wampserver.com/en/

.. _`Install Composer`: https://getcomposer.org/download/

.. _`recommended commands`: http://symfony.com/doc/current/setup/file_permissions.html

.. _`example configurations`: http://symfony.com/doc/current/setup/web_server_configuration.html

.. _`DHIL Documentation Guide`: https://github.com/sfu-dhil/dhil-docs-guide
