<?php

namespace Rfuehricht\Configloader\Middleware;

use Rfuehricht\Configloader\Utility\ConfigurationUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;


final readonly class LoadConfiguration implements MiddlewareInterface
{

    public function __construct(
        private ConfigurationUtility $configurationUtility
    )
    {

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        /** @var Site $site */
        $site = $request->getAttribute('site');
        if ($site === null || $site instanceof NullSite) {

            $site = $this->configurationUtility->determineSiteFromRequestParameters($request);

        }

        if ($site && !($site instanceof NullSite)) {
            $this->configurationUtility->loadConfiguration($site);
        }
        return $handler->handle($request);
    }
}