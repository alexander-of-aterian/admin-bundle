Feature: Deleting existing object
  In order to allow deleting existing news
  As a developer
  I need to install FSiAdminBundle and configure delete admin element for newsletter subscribers

  Background:
    Given the following admin elements were registered
      | Id                | Class                                     |
      | subscriber        | FSi\FixturesBundle\Admin\Subscriber       |
      | subscriber_delete | FSi\FixturesBundle\Admin\SubscriberDelete |
    And there are 3 subscribers in database

  Scenario: Display delete action
    Given I am on the "Subscribers list" page
    And translations are enabled in application
    Then I should see actions dropdown with following options
      | Option        |
      | Select action |
      | Delete        |
    And I should see confirmation button "Ok"

  @javascript
  Scenario: Delete single subscriber
    Given I am on the "Subscribers list" page
    When I press checkbox in first column in first row
    And I choose action "Delete" from actions
    And I press confirmation button "Ok"
    Then I should be redirected to "Subscribers list" page
    And there should be 2 subscribers in database
    And I should see a success message saying:
    """
    Operation has been completed successfully.
    """

  @javascript
  Scenario: Delete all elements from page
    Given I am on the "Subscribers list" page
    When I press checkbox in first column header
    And I choose action "Delete" from actions
    And I press confirmation button "Ok"
    Then I should be redirected to "Subscribers list" page
    And there should not be any subscribers in database
    And I should see a success message saying:
    """
    Operation has been completed successfully.
    """
