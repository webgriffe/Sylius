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

namespace Sylius\Bundle\UiBundle\ContextProvider;

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

/**
 * @experimental
 */
interface ContextProviderInterface
{
    public function provide(array $templateContext, TemplateBlock $templateBlock): array;

    public function supports(TemplateBlock $templateBlock): bool;
}