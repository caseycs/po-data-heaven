Feature: Report without parameters

  Background:
     Given ensure report "messages-all.yml" presented
       And messages table exists
       And message stored with user_id "1" and content "Hello buddy 1"
       And message stored with user_id "1" and content "Hello buddy 2"
       And message stored with user_id "2" and content "I like it 1"
       And message stored with user_id "2" and content "I like it 2"

  Scenario: Open report without parameters
    Given I am on the homepage
    Then I follow "Messages: all"
      And the url should match "report/messages-all/result"
      And I should see "All messages report description"
      And I should see "Hello buddy 1"
      And I should see "Hello buddy 2"
      And I should see "I like it 1"
      And I should see "I like it 2"
