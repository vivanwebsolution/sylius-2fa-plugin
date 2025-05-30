# Sylius/Sylius:features/admin/channel/managing_channels/adding_channel.feature

@managing_channels
Feature: Adding a new channel
    In order to sell through multiple websites or mobile applications
    As an Administrator
    I want to add a new channel to the registry

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And the store operates in "United States" and "Poland"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new channel
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile channel" should appear in the registry

    @api @ui
    Scenario: Adding a new channel with additional fields
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I describe it as "Main distribution channel for mobile apps"
        And I set its hostname as "m.avengers-gear.com"
        And I set its contact email as "contact@avengers-gear.com"
        And I set its contact phone number as "11331122"
        And I define its color as "blue"
        And I choose "Euro" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I choose "United States" and "Poland" as operating countries
        And I select the "Order items based" as tax calculation strategy
        And I allow to skip shipping step if only one shipping method is available
        And I allow to skip payment step if only one payment method is available
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile channel" should appear in the registry
