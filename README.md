# OpenClassroom_P6

## Installation

> * Clone the git repositroy
    >   * git clone git@github.com:Raphael-Marchese/snowTricks.git
    >   * cd snowTricks
> * build docker
    >   * docker compose build
> * Run the docker container
    >   * docker compose up -d

> * If you want to fill the database with fixtures
    >   * php bin/console doctrine:migrations:migrate
    >   * php bin/console doctrine:fixtures:load
> * To start the local server
    >   * symfony serve 