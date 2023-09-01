.. _update:

Updates
=======

Applying updates from git shouldn't be difficult.

1. Get the updates from a git remote:

.. code-block:: bash
   
  git pull

if you are working on a personal fork of the project, use the following to get the changes from main repository

.. code-block:: bash
   
  git pull <main_repository_link>

2. Update the git submodules:

.. code-block:: bash

  git submodule update --recursive --remote


3. Install any updated composer dependencies:

.. code-block:: bash

  php -d memory_limit=-1 ./vendor/bin/composer install -o


4. Apply any database schema updates:

.. code-block:: bash

  ./bin/console doctrine:schema:update --force

  
5. Update the web assets:
  
.. code-block:: bash

  bower install


6. Clear the cache: 

.. code-block:: bash

  ./bin/console cache:clear --env=prod


7. To commit the changes you have made, start with checking the status of your copy first:

.. code-block:: bash

  git status

you can restore any deleted/damaged files by typing the following:

.. code-block:: bash

  git checkout -- <file_name>

8. Commit and push your updates:

.. code-block:: bash

  git add .
  git commit -m "explanation"
  git push


That should be it.
