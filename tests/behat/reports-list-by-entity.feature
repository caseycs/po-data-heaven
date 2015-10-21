Feature: Reports list by entity

  Background:
    Given ensure report "messages-all.yml" presented
    Given ensure report "messages-of-user.yml" presented
    Given ensure report "user-details.yml" presented
    And users table exists
    And messages table exists
    And message stored with user_id "1" and content "Hello buddy 1"

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
