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

namespace Sylius\Behat\Service;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

abstract class AutocompleteHelper
{
    /**
     * @param string $value
     */
    public static function chooseValue(Session $session, NodeElement $element, $value)
    {
        static::activateAutocompleteDropdown($session, $element);

        $element->find('css', sprintf('div.item:contains("%s")', $value))->click();

        static::waitForElementToBeVisible($session, $element);
    }

    /**
     * @param string[] $values
     */
    public static function chooseValues(Session $session, NodeElement $element, array $values)
    {
        static::activateAutocompleteDropdown($session, $element);

        foreach ($values as $value) {
            $element->find('css', sprintf('div.item:contains("%s")', $value))->click();

            JQueryHelper::waitForAsynchronousActionsToFinish($session);
        }

        static::waitForElementToBeVisible($session, $element);
    }

    public static function searchForValue(Session $session, NodeElement $element, string $phrase)
    {
        static::activateAutocompleteDropdown($session, $element);

        $element->find('css', 'input.search')->setValue($phrase);

        self::waitForStartSearching($session);
        JQueryHelper::waitForAsynchronousActionsToFinish($session);
    }


    public static function getSearchResults(NodeElement $element)
    {
        return array_map(
            function (NodeElement $result) {
                return $result->getText();
            },
            $element->findAll('css', 'div.item')
        );
    }

    private static function activateAutocompleteDropdown(Session $session, NodeElement $element)
    {
        JQueryHelper::waitForAsynchronousActionsToFinish($session);

        $element->click();

        JQueryHelper::waitForAsynchronousActionsToFinish($session);
        static::waitForElementToBeVisible($session, $element);
    }

    private static function waitForElementToBeVisible(Session $session, NodeElement $element)
    {
        $session->wait(5000, sprintf(
            '$(document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue).dropdown("is visible")',
            str_replace('"', '\"', $element->getXpath())
        ));
    }

    private static function waitForStartSearching(Session $session)
    {
        $session->wait(5000, '$(\'.loading\').length > 0');
    }
}
