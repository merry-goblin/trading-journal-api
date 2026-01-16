<?php

namespace App\Security\Api\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

abstract class ApiAuthenticationException extends AuthenticationException {}
