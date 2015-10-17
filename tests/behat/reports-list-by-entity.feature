Feature: Reports list by entity

Scenario: Reports list by entity
  Given I am on the homepage
  Then I follow "Messages: all"
    And the url should match "report/messages-all/result"
    And the response should contain "by-entity/user/1"
  Then I am on "by-entity/user/1"
    And I should see "Reports with entities"

Scenario: Follow the only this entity report link
  Given I am on "by-entity/user/1"
    And I should see "Messages of user"
    And I should see "User details"
  Then I follow "Messages of user"
    And the url should match "report/messages-of-user/result"
  And I should see "Messages of user"
