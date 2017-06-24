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
use Prooph\EventStore\Http\Api\Action\DeleteProjection;
use Prooph\EventStore\Projection\ProjectionManager;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

class DeleteProjectionTest extends TestCase
{
    /**
     * @test
     */
    public function it_will_delete_projection_incl_emitted_events(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->deleteProjection('runner', true)->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('name')->willReturn('runner')->shouldBeCalled();
        $request->getAttribute('deleteEmittedEvents')->willReturn('true')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new DeleteProjection($projectionManager->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function it_will_delete_projection_without_emitted_events(): void
    {
        $projectionManager = $this->prophesize(ProjectionManager::class);
        $projectionManager->deleteProjection('runner', false)->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute('name')->willReturn('runner')->shouldBeCalled();
        $request->getAttribute('deleteEmittedEvents')->willReturn('false')->shouldBeCalled();

        $delegate = $this->prophesize(DelegateInterface::class);

        $action = new DeleteProjection($projectionManager->reveal());

        $response = $action->process($request->reveal(), $delegate->reveal());

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
    }
}
