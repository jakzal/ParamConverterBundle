<?php

namespace Zalas\Bundle\ParamConverterBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceParamConverter implements ParamConverterInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $object = $this->callService($request, $configuration);

        $class = $configuration->getClass();
        if ($object instanceof $class) {
            $request->attributes->set($configuration->getName(), $object);

            return true;
        }

        if ($configuration->isOptional()) {
            return false;
        }

        throw new NotFoundHttpException(sprintf('Object of class "%s" not found.', $class));

    }

    /**
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        $options = $configuration->getOptions();

        return !empty($options['service']) && !empty($options['method']);
    }

    /**
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return mixed
     */
    private function callService(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions();
        $callable = $this->getCallable($options);

        return call_user_func_array($callable, $this->extractArguments($request, $options));
    }

    /**
     * @param Request $request
     * @param array   $options
     *
     * @return array
     */
    private function extractArguments(Request $request, array $options)
    {
        $arguments = isset($options['arguments']) && is_array($options['arguments']) ? $options['arguments'] : [];

        return array_map(
            function ($argument) use ($request) {
                return $request->attributes->get($argument);
            },
            $arguments
        );
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getCallable(array $options)
    {
        $serviceId = $options['service'];
        $method = $options['method'];
        $service = $this->container->get($serviceId);
        $callable = [$service, $method];

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(sprintf(
                'The param converter is configured to use a non existent method "%s::%s()" on the "%s" service.',
                get_class($service), $method, $serviceId
            ));
        }

        return $callable;
    }
}
