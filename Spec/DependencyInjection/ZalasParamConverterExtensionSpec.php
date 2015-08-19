<?php

namespace Spec\Zalas\Bundle\ParamConverterBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Extension\Extension;

class ZalasParamConverterExtensionSpec extends ObjectBehavior
{
    function it_is_a_dependency_injection_extension()
    {
        $this->shouldHaveType(Extension::class);
    }
}
