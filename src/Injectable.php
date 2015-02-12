<?php

namespace Disclosure;

interface Injectable
{
    public function inject(callable $inject);
}

