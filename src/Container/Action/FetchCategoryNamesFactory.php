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

namespace Prooph\EventStore\Http\Api\Container\Action;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\Http\Api\Action\FetchCategoryNames;
use Prooph\EventStore\Http\Api\Transformer\JsonTransformer;
use Psr\Container\ContainerInterface;

final class FetchCategoryNamesFactory
{
    public function __invoke(ContainerInterface $container): FetchCategoryNames
    {
        $actionHandler = new FetchCategoryNames($container->get(EventStore::class));

        $actionHandler->addTransformer(
            new JsonTransformer(),
            'application/atom+json',
            'application/json'
        );

        return $actionHandler;
    }
}