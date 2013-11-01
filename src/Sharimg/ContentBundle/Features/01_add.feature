Feature: Content feature
@content

Scenario: 1- Test page
    Given I am on "/content/add"
    Then I should see "Share content"

Scenario: 2- Add a content using url_content
    Given I am on "/content/add"
    When I fill in "description" with "Google Logo (behat)"
    And I fill in "media_url" with "https://www.google.fr/images/srpr/logo11w.png"
    And I press "submit-content"
    Then I should see "Google Logo (behat)"

Scenario: 3- Add a content using url_content
    Given I am on "/content/add"
    When I fill in "description" with "Google Logo2 (behat)"
    And I attach the file "data/content.png" to "file_input"
    And I press "submit-content"
    Then I should see "Google Logo2 (behat)"