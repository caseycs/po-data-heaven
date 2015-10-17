Feature: Report with config

Scenario: Display report with config
  Given I am on the homepage
  Then I follow "Messages of user"
    And the url should match "report/messages-of-user"
    And I should see "User ID"
  Then I fill in "user_id" with "2"
  Then I press "Generate"
    And the url should match "report/messages-of-user/result"
    And I should see "Messages of user"
    And I should see "Messages of user report description"
    And I should not see "Hello buddy 1"
    And I should not see "Hello buddy 2"
    And I should see "I like it 1"
    And I should see "I like it 2"
