<?php

namespace Monolyth\Disclosure;

use Psr\Container\NotFoundExceptionInterface;
use DomainException;

class NotFoundException extends DomainException implements NotFoundExceptionInterface
{
}

