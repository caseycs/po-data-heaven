Feature: Report with YAML logic or parse error should be displayed on the homepage

  Scenario: YAML with parse error
    Given ensure report "invalid.yml" presented
    Given I am on the homepage
      And I should see "Reports parsing failed"
      And I should see "invalid.yml"

  Scenario: YAML with Logic error
    Given ensure report "report-with-logic-error.yml" presented
    Given I am on the homepage
      And I should see "Reports parsing failed"
      And I should see "report-with-logic-error.yml"
