Feature: Analytics feature
@analytics

Scenario: 50.1 - Test menu without permission
    Given I am on homepage
    Then I should not see "Analitycs"

Scenario: 50.2 - Test menu logged without permission
    Given I am logged as "test" password "test123"
    And I am on homepage
    Then I should not see "Analytics"

Scenario: 50.3 - Test menu loggued as admin
    Given I am logged as "admin" password "admin123"
    And I am on homepage
    Then I should see "Analytics"

Scenario: 50.4 - Test without permission
    Given I am on "/admin/analytics"
    Then I should not see "Analytics"
    And I should not see "Search"

Scenario: 50.5 - Test as admin
    Given I am on homepage
    And I am logged as "admin" password "admin123"
    When I follow "Analytics"
    Then I should see "Analytics"
    And I should see "Search"
