Feature: Report has CSV export

  Background:
    Given ensure report "messages-all.yml" presented
      And messages table exists
      And message stored with user_id "1" and content "msg"

  Scenario: Open report and download CSV
  Given I am on the homepage
  Then I follow "Messages: all"
    And the "a span.glyphicon-download-alt" element should contain ""
