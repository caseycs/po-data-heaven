Feature: Wrong report name should cause predefined error message

  Scenario: Report config page
    Given I am on "report/unexisted"
    Then the response status code should be 404

  Scenario: Report result page
    Given I am on "report/unexisted/result"
    Then the response status code should be 404

  Scenario: Report CSV page
    Given I am on "report/unexisted/csv"
    Then the response status code should be 404
