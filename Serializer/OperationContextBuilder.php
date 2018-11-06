<?php

namespace Dontdrinkandroot\AppIntegrationBundle\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class OperationContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface
     */
    private $decoratedBuilder;

    public function __construct(SerializerContextBuilderInterface $decoratedBuilder)
    {
        $this->decoratedBuilder = $decoratedBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decoratedBuilder->createFromRequest($request, $normalization, $extractedAttributes);

        $operationPrefix = null;
        $resourceClass = null;
        $subresourceProperty = null;
        $customOperationName = null;

        switch ($context['operation_type']) {
            case 'collection':
                switch ($context['collection_operation_name']) {
                    case 'get':
                        $operationPrefix = 'list';
                        break;
                    case 'post':
                        if (!$normalization) {
                            $operationPrefix = 'post';
                        } else {
                            $operationPrefix = 'get';
                        }
                        break;
                }
                $resourceClass = $this->getShortName($context['resource_class']);
                break;
            case 'item':
                switch ($context['item_operation_name']) {
                    case 'get':
                        $operationPrefix = 'get';
                        break;
                    case 'put':
                        if (!$normalization) {
                            $operationPrefix = 'put';
                        } else {
                            $operationPrefix = 'get';
                        }
                        break;
                    default:
                        $customOperationName = $context['item_operation_name'];
                        if (!$normalization) {
                            $operationPrefix = strtolower($request->getMethod());
                        } else {
                            $operationPrefix = 'get';
                        }
                }
                $resourceClass = $this->getShortName($context['resource_class']);
                break;
            case 'subresource':
                if ($this->isSubresourceCollection($request)) {
                    $operationPrefix = 'list';
                } else {
                    $operationPrefix = 'get';
                }
                $resourceClass = $this->getShortName(key($context['subresource_resources']));
                $subresourceProperty = $this->getSubresourceProperty($request);
                break;
        }

        $group = $operationPrefix . '.' . $resourceClass;
        if (null != $subresourceProperty) {
            $group .= '.' . $subresourceProperty;
        }
        if (null != $customOperationName) {
            $group .= '.' . $customOperationName;
        }
        $context['groups'][] = $group;

        return $context;
    }

    private function getShortName(string $class): string
    {
        $reflectedClass = new \ReflectionClass($class);

        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $reflectedClass->getShortName()));
    }

    private function getSubresourceProperty(Request $request): string
    {
        $subResourceContext = $request->attributes->get('_api_subresource_context');

        return $subResourceContext['property'];
    }

    private function isSubresourceCollection($request)
    {
        $subResourceContext = $request->attributes->get('_api_subresource_context');

        return $subResourceContext['collection'];
    }
}
