Feature: Report has CSV export

Scenario: Open report and download CSV
  Given I am on the homepage
  Then I follow "Messages: all"
    And the "a span.glyphicon-download-alt" element should contain ""
