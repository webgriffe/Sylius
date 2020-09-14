@managing_products
Feature: Taxons autocomplete
    In order to get hints when looking for taxons to be set as product's main taxon
    As an Administrator
    I want to get taxons according to my specified phrase

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts", "Watches", "Belts" and "Wallets"
        And the store has a product "T-Shirt Batman"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Getting a hint when looking for taxons
        When I want to modify the "T-Shirt Batman" product
        And I look for a taxon with "b" in name to set as main taxon
        Then I should see 1 taxons on the list
        And I should see the taxon named "Belts" in the list

    @ui @javascript
    Scenario: Getting a hint when looking for taxons
        When I look for a taxon with "shi" in name to set as main taxon
        Then I should see 1 taxons on the list
        And I should see the taxon named "T-Shirts" in the list

    @ui @javascript
    Scenario: Getting a hint when looking for taxons
        When I look for a taxon with "e" in name to set as main taxon
        Then I should see 3 taxons on the list
        And I should see the taxon named "Belts", "Wallets" and "Watches" in the list

    @ui @javascript @todo
    Scenario: Getting a taxon from its code
        When I want to get taxon with "belts" code
        Then I should see 1 taxons on the list
        And I should see the taxon named "Belts" in the list
