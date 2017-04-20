<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStore\Http\Api\Action;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\HasStream;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Prooph\EventStore\StreamName;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\JsonResponse;

class HasStreamTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_415_when_invalid_accept_header_sent(): void
    {
        $eventStore = $this->prophesize(EventStore::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('')->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('foo\bar')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new HasStream($eventStore->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/vnd.eventstore.atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(415, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_false_when_stream_not_found(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->hasStream(new StreamName('unknown'))->willReturn(false)->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('unknown')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new HasStream($eventStore->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/vnd.eventstore.atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['result' => false], json_decode($response->getBody()->getContents(), true));
    }

    /**
     * @test
     */
    public function it_returns_true_when_stream_found(): void
    {
        $eventStore = $this->prophesize(EventStore::class);
        $eventStore->hasStream(new StreamName('known'))->willReturn(true)->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('Accept')->willReturn('application/vnd.eventstore.atom+json')->shouldBeCalled();
        $request->getAttribute('streamname')->willReturn('known')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new HasStream($eventStore->reveal());
        $action->addTransformer(new JsonTransformer(), 'application/vnd.eventstore.atom+json');

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['result' => true], json_decode($response->getBody()->getContents(), true));
    }
}
