## Installation

1. Create a `.env` file from `.env.dist` 

    ```sh
    $ cp .env.dist .env && nano .env
    $ cd app && cp .env.dist .env
    ```

2. Build and run the stack in detached mode (stop any system's ngixn/apache2 service first)

    ```sh
    $ docker-compose build
    $ docker-compose up -d
    ```

3. Prepare the Symfony application
    1. Update Symfony env variables (*.env*)

        ```
        #...
        DATABASE_URL=mysql://db_user:db_password@mysql:3306/db_name
        #...
        ```

    2. Composer install & update the schema from the container, execute migrations

        ```sh
        $ docker-compose exec php-fpm bash
        $ composer install
        $ bin/console doctrine:schema:update --force
        $ bin/console doctrine:migrations:migrate
        ```
    3. Generate the SSL keys
        ```sh
        $ bin/console lexik:jwt:generate-keypair
        ```

Now we can stop our stack with `docker-compose down` and start it again with `docker-compose up -d`