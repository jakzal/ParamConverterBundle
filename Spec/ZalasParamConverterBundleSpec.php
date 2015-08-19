<?php

namespace Spec\Zalas\Bundle\ParamConverterBundle;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ZalasParamConverterBundleSpec extends ObjectBehavior
{
    function it_is_a_bundle()
    {
        $this->shouldHaveType(Bundle::class);
    }
}
