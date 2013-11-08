Feature: Import feature
@import

Scenario: 40.1 - Import home page
	  Given I am on "/import"
	  Then I should see "These tools allow you to import data from other application"

Scenario: 40.2 - Import from twitter usertimeline
	  Given I am on "/import/twitter/usertimeline/twitter"
	  Then I should see "Timeline: twitter"


Scenario: 40.3 - Import from twitter hometimeline
	  Given I am on "/import/twitter/hometimeline/twitter"
	  Then I should see "Timeline: twitter"
