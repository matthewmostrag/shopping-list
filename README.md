Shopping List
=============

Shopping List allow you to create easily your shopping lists. This is how to install the project.

*(This was a little test for a job.)*

Clone the repository
=============

First of all clone the repository.

    git clone git@github.com:matthewmostrag/shopping-list.git
    
Then go to the project directory just created.

    cd shopping-list
    
Install the dependencies
=============

Run composer to install the project dependecies and setup your database configuration.

    composer install
    
Create the database schema
=============

Create the application data schema.

    php bin/console doctrine:schema:create
    
Load the fixtures
=============

There is default data to start with the project and discover it. 

    php bin/console doctrine:fixtures:load
    
You're done
=============

The project is now fully working, have fun!
