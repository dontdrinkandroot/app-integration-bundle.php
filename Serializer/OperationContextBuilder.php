<?php

namespace Dontdrinkandroot\AppIntegrationBundle\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ReflectionClass;
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
        $readOperation = true;

        switch ($context['operation_type']) {

            case 'collection':
                switch ($context['collection_operation_name']) {
                    case 'get':
                        $operationPrefix = 'list';
                        break;
                    case 'post':
                        $operationPrefix = $normalization ? 'get' : 'post';
                        $readOperation = $normalization;
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
                        $operationPrefix = $normalization ? 'get' : 'put';
                        $readOperation = $normalization;
                        break;
                    default:
                        $customOperationName = $context['item_operation_name'];
                        $operationPrefix = $normalization ? 'get' : strtolower($request->getMethod());
                        $readOperation = $normalization;
                }
                $resourceClass = $this->getShortName($context['resource_class']);
                break;

            case 'subresource':
                $operationPrefix = $this->isSubresourceCollection($request) ? 'list' : 'get';
                $resourceClass = $this->getShortName(key($context['subresource_resources']));
                $subresourceProperty = $this->getSubresourceProperty($request);
                break;
        }

        $groupSuffix = $resourceClass;

        if (null != $subresourceProperty) {
            $groupSuffix .= '.' . $subresourceProperty;
        }

        if (null != $customOperationName) {
            $groupSuffix .= '.' . $customOperationName;
        }

        $context['groups'][] = $operationPrefix . '.' . $groupSuffix;
        $context['groups'][] = 'all' . '.' . $groupSuffix;
        $context['groups'][] = ($readOperation ? 'read' : 'write') . '.' . $groupSuffix;
        $context['groups'][] = $readOperation ? 'api_read' : 'api_write';

        return $context;
    }

    private function getShortName(string $class): string
    {
        $reflectedClass = new ReflectionClass($class);

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
