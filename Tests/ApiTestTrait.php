<?php

namespace Dontdrinkandroot\AppIntegrationBundle\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

trait ApiTestTrait
{
    protected $acceptedJsonContentTypes = [
        'application/json',
        'application/json; charset=utf-8',
        'application/problem+json; charset=utf-8',
    ];

    protected function jsonGet(
        string $uri,
        array $parameters = [],
        array $headers = []
    ): Response {
        return $this->jsonRequest(Request::METHOD_GET, $uri, $parameters, $headers);
    }

    protected function jsonPut(
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null
    ): Response {
        return $this->jsonRequest(Request::METHOD_PUT, $uri, $parameters, $headers, $content);
    }

    protected function jsonPost(
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null
    ): Response {
        return $this->jsonRequest(Request::METHOD_POST, $uri, $parameters, $headers, $content);
    }

    protected function jsonDelete(
        string $uri,
        array $parameters = [],
        array $headers = []
    ): Response {
        return $this->jsonRequest(Request::METHOD_DELETE, $uri, $parameters, $headers);
    }

    protected function jsonRequest(
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        ?array $content = null
    ): Response {
        $client = $this->getClient();
        $client->request(
            $method,
            $uri,
            $parameters,
            [],
            $this->transformHeaders($headers),
            $this->jsonEncodeContent($content)
        );

        return $client->getResponse();
    }

    protected function assertJsonResponse(Response $response, $statusCode = 200)
    {
        if (Response::HTTP_NO_CONTENT !== $statusCode) {

            Assert::assertTrue(
                $this->hasJsonContentType($response),
                sprintf('JSON content type missing, given: %s', $response->headers->get('Content-Type'))
            );
        }

        $content = $response->getContent();
        $decodedContent = json_decode($content, true);

        Assert::assertEquals($statusCode, $response->getStatusCode(), $content);

        return $decodedContent;
    }

    private function hasJsonContentType($response)
    {
        foreach ($this->acceptedJsonContentTypes as $acceptedJsonContentType) {
            if ($response->headers->contains('Content-Type', $acceptedJsonContentType)) {
                return true;
            }
        }

        return false;
    }

    protected function jsonEncodeContent(?array $content): ?string
    {
        if (null === $content) {
            return null;
        }

        return json_encode($content);
    }

    protected function transformHeaders(array $headers)
    {
        $transformedHeaders = [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/json',
        ];
        foreach ($headers as $key => $value) {
            if (strpos($key, 'PHP_') !== 0) {
                $transformedHeaders['HTTP_'.$key] = $value;
            } else {
                $transformedHeaders[$key] = $value;
            }
        }

        return $transformedHeaders;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->getContainer()->get('test.client');
    }

    protected function createJwtAuthorizationHeader(UserInterface $user, array $headers = [])
    {
        $token = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);
        $headers['Authorization'] = 'Bearer '.$token;

        return $headers;
    }

    protected function createBasicAuthorizationHeader(UserInterface $user, array $headers = []): array
    {
        $headers['PHP_AUTH_USER'] = $user->getUsername();
        $headers['PHP_AUTH_PW'] = $user->getUsername();

        return $headers;
    }

    protected abstract function getContainer(): ContainerInterface;
}
