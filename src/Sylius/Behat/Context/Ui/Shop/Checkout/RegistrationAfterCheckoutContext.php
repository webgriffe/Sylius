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

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Shop\Account\RegisterElementInterface;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterThankYouPageInterface;
use Sylius\Behat\Page\Shop\Account\VerificationPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webmozart\Assert\Assert;

final class RegistrationAfterCheckoutContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var LoginPageInterface */
    private $loginPage;

    /** @var ThankYouPageInterface */
    private $thankYouPage;

    /** @var HomePageInterface */
    private $homePage;

    /** @var VerificationPageInterface */
    private $verificationPage;

    /** @var RegisterThankYouPageInterface */
    private $registerThankYouPage;

    /** @var DashboardPageInterface */
    private $dashboardPage;

    /** @var RegisterElementInterface */
    private $registerElement;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        LoginPageInterface $loginPage,
        ThankYouPageInterface $thankYouPage,
        HomePageInterface $homePage,
        VerificationPageInterface $verificationPage,
        RegisterThankYouPageInterface $registerThankYouPage,
        DashboardPageInterface $dashboardPage,
        RegisterElementInterface $registerElement,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->loginPage = $loginPage;
        $this->thankYouPage = $thankYouPage;
        $this->homePage = $homePage;
        $this->verificationPage = $verificationPage;
        $this->registerThankYouPage = $registerThankYouPage;
        $this->dashboardPage = $dashboardPage;
        $this->registerElement = $registerElement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @When I specify a password as :password
     */
    public function iSpecifyThePasswordAs(string $password): void
    {
        $this->registerElement->specifyPassword($password);
        $this->sharedStorage->set('password', $password);
    }

    /**
     * @When /^I confirm (this password)$/
     */
    public function iConfirmThisPassword(string $password): void
    {
        $this->registerElement->verifyPassword($password);
    }

    /**
     * @When I register this account
     */
    public function iRegisterThisAccount(): void
    {
        $this->registerElement->register();
    }

    /**
     * @When I verify my account using link sent to :customer
     */
    public function iVerifyMyAccountUsingLink(CustomerInterface $customer): void
    {
        $user = $customer->getUser();
        Assert::notNull($user, 'No account for given customer');

        $this->verificationPage->verifyAccount($user->getEmailVerificationToken());
    }

    /**
     * @Then the registration form should be prefilled with :email email
     */
    public function theRegistrationFormShouldBePrefilledWithEmail(string $email): void
    {
        $this->thankYouPage->createAccount();

        Assert::same($this->registerElement->getEmail(), $email);
    }

    /**
     * @Then I should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();

        Assert::true($this->homePage->hasLogoutButton());
    }

    /**
     * @Then /^I should be on registration thank you page$/
     */
    public function iShouldBeOnRegistrationThankYouPage(): void
    {
        $registeredCustomer = $this->customerRepository->findLatest(1)[0];
        Assert::true($this->registerThankYouPage->isOpen(['id' => $registeredCustomer->getId()]));
    }

    /**
     * @Then /^I should be on my account dashboard/
     */
    public function iShouldBeOnMyAccountDashboard()
    {
        Assert::true($this->dashboardPage->isOpen());
    }
}
