Feature: User feature
@user

Scenario: 30.1 - Page login
        Given I am on "/login"
        Then I should see "Username:"
        And I should see "Password:"
        And I should see "Login"

Scenario: 30.2 - Login error
        Given I am on "/login"
        And I fill in "_username" with "badusername"
        And I fill in "_password" with "badpassword"
        When I press "_submit"
        Then I should be on "/login"
        And I should see "Invalid username or password"

Scenario: 30.3 - Login success
        Given I am on "/login"
        And I fill in "_username" with "test"
        And I fill in "_password" with "test123"
        When I press "_submit"
        Then I should not see "Invalid username or password"
        And I should see "Logged in as test"
