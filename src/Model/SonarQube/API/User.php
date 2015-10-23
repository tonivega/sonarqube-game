<?php

namespace Privalia\SQHeal\Model\SonarQube\API;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Underscore\Underscore as _;

class User {

    public function __construct(array $config){
        $this->config = $config;
    }

    public function getGroupDetails() {
        return $this->config['app']['user-groups'];
    }

}
