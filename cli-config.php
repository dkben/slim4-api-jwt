<?php

// cli-config.php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once "bootstrap.php";

return ConsoleRunner::createHelperSet($entityManager);
