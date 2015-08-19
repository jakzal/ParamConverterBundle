<?php

namespace Spec\Zalas\Bundle\ParamConverterBundle\Request\ParamConverter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceParamConverterSpec extends ObjectBehavior
{
    function let(ContainerInterface $container, Request $request, ParameterBag $attributes)
    {
        $this->beConstructedWith($container);

        $request->attributes = $attributes;
    }

    function it_is_a_param_converter()
    {
        $this->shouldHaveType(ParamConverterInterface::class);
    }

    function it_supports_a_configuration_if_both_service_and_method_are_provided()
    {
        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => MyArgument::class,
            'options' => [
                'service' => 'my_service',
                'method' => 'myMethod',
            ],
        ]);

        $this->supports($configuration)->shouldReturn(true);
    }

    function it_does_not_support_a_configuration_if_service_is_missing()
    {
        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => MyArgument::class,
            'options' => [
                'method' => 'myMethod',
            ],
        ]);

        $this->supports($configuration)->shouldReturn(false);
    }

    function it_does_not_support_a_configuration_if_method_is_missing()
    {
        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => MyArgument::class,
            'options' => [
                'service' => 'my_service',
            ],
        ]);

        $this->supports($configuration)->shouldReturn(false);
    }

    function it_calls_the_service_to_provide_an_argument(ContainerInterface $container, Request $request, ParameterBag $attributes)
    {
        $container->get('my_service')->willReturn(new MyService());

        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => MyArgument::class,
            'options' => [
                'service' => 'my_service',
                'method' => 'myMethod',
            ],
        ]);

        $attributes->set('foo', new MyArgument())->shouldBeCalled();

        $this->apply($request, $configuration)->shouldReturn(true);
    }

    function it_calls_the_service_with_configured_arguments(ContainerInterface $container, Request $request, ParameterBag $attributes)
    {
        $container->get('my_service')->willReturn(new MyService());

        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => MyArgument::class,
            'options' => [
                'service' => 'my_service',
                'method' => 'myMethod',
                'arguments' => ['id', 'name']
            ],
        ]);

        $attributes->get('id')->willReturn(42);
        $attributes->get('name')->willReturn('Bob');

        $attributes->set('foo', new MyArgument(42, 'Bob'))->shouldBeCalled();

        $this->apply($request, $configuration);
    }

    function it_throws_an_invalid_argument_exception_if_method_is_not_available_on_the_service(ContainerInterface $container, Request $request)
    {
        $container->get('my_service')->willReturn(new MyService());

        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => MyArgument::class,
            'options' => [
                'service' => 'my_service',
                'method' => 'invalidMethod',
            ],
        ]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringApply($request, $configuration);
    }

    function it_throws_a_not_found_http_exception_if_service_does_not_return_an_object_of_configured_class(ContainerInterface $container, Request $request)
    {
        $container->get('my_service')->willReturn(new MyService());

        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => \stdClass::class,
            'options' => [
                'service' => 'my_service',
                'method' => 'myMethod',
            ],
        ]);

        $this->shouldThrow(NotFoundHttpException::class)->duringApply($request, $configuration);
    }

    function it_returns_false_if_arugment_is_optional(ContainerInterface $container, Request $request)
    {
        $container->get('my_service')->willReturn(new MyService());

        $configuration = new ParamConverter([
            'name' => 'foo',
            'class' => \stdClass::class,
            'isOptional' => true,
            'options' => [
                'service' => 'my_service',
                'method' => 'myMethod',
            ],
        ]);

        $this->apply($request, $configuration)->shouldReturn(false);
    }
}

class MyService
{
    public function myMethod($id = null, $name = null)
    {
        return new MyArgument($id, $name);
    }
}

class MyArgument
{
    private $id;
    private $name;

    public function __construct($id = null, $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}