Feature: Report without parameters

Scenario: Reports list on home page
  Given I am on the homepage
  Then I follow "Messages: all"
    And the url should match "report/messages-all/result"
    And I should see "All messages report description"
    And I should see "Hello buddy 1"
    And I should see "Hello buddy 2"
    And I should see "I like it 1"
    And I should see "I like it 2"
