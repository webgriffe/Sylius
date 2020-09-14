<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxonsContext implements Context
{
    /** @var Client */
    private $client;

    /** @var SessionInterface */
    private $session;

    public function __construct(Client $client, SessionInterface $session)
    {
        $this->client = $client;
        $this->session = $session;
    }

    /**
     * @When I want to get taxon with :code code
     */
    public function iWantToGetTaxonWithCode($code)
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/leaf', ['code' => $code], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When /^I want to get children from (taxon "[^"]+")/
     */
    public function iWantToGetChildrenFromTaxon(TaxonInterface $taxon)
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/leafs', ['parentCode' => $taxon->getCode()], [], ['ACCEPT' => 'application/json']);
    }

    /**
     * @When I want to get taxon root
     */
    public function iWantToGetTaxonRoot()
    {
        $this->client->getCookieJar()->set(new Cookie($this->session->getName(), $this->session->getId()));
        $this->client->request('GET', '/admin/ajax/taxons/root-nodes', [], [], ['ACCEPT' => 'application/json']);
    }
}
