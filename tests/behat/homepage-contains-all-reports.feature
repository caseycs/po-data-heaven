Feature: Homepage contains all reports

Scenario: Reports list on home page
  Given I am on the homepage
    And I should see "Product Owner Data Heaven"
    And I should see "All reports"
    And I should see "User details"
    And I should see "Messages: all"
    And I should see "Messages of user"
