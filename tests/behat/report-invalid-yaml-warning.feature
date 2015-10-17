Feature: Report with YAML logic or parse error should be displayed on the homepage

  Scenario: YAML with parse error
    Given ensure YAML report with parse error presented
    Given I am on the homepage
      And I should see "Reports parsing failed"
      And I should see "invalid.yml"
    Then ensure YAML report with parse error removed

  Scenario: YAML with Logic error
    Given ensure YAML report with logic error presented
    Given I am on the homepage
      And I should see "Reports parsing failed"
      And I should see "report-with-logic-error.yml"
    Then ensure YAML report with logic error removed
