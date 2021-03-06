<?php
/**
 * This file is part of the prooph/event-store-http-api.
 * (c) 2016-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStore\Http\Api\Integration;

use GuzzleHttp\Psr7\Request;

/**
 * @group integration
 */
class PostStreamTest extends AbstractHttpApiServerTestCase
{
    /**
     * @test
     */
    public function it_posts_stream(): void
    {
        $this->createTestStream();

        $this->appendTo();
    }

    private function appendTo(): void
    {
        $request = new Request(
            'POST',
            'http://localhost:8080/stream/teststream',
            [
                'Content-Type' => 'application/vnd.eventstore.atom+json',
            ],
            '[
              {
                "message_name":"var",
                "payload":{"b" : "c"},
                "metadata":{"_aggregate_version":2}
              }
            ]'
        );

        $response = $this->client->sendRequest($request);

        $this->assertSame(204, $response->getStatusCode(), (string) $response->getBody());
    }
}
