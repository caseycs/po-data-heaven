Feature: Report pages contain edit button

  Background:
    Given ensure report "messages-of-user.yml" presented
    And messages table exists
    And message stored with user_id "2" and content "msg"

  Scenario: Report config page
    Given I am on the homepage
    Then I follow "Messages of user"
      And the "a span.glyphicon-pencil" element should contain ""

  Scenario: Report result page
    Given I am on the homepage
    Then I follow "Messages of user"
    Then I fill in "user_id" with "2"
    Then I press "Generate"
      And the "a span.glyphicon-pencil" element should contain ""

