Feature: Homepage without reports

Scenario: Reports list on home page
  Given I am on the homepage
    And I should see "Product Owner Data Heaven"
    And I should see "All reports"
    And the response status code should be 200
