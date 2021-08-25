Feature: User 
  In order to know a user well
  As a api interface
  I need to get info via uid

  Scenario: get user info via uid
    Given I go to "/user/1"
    Then the response status code should be 200
    And I see the Json 
    """
    {"code":0,"message":"","data":{"id":"1","name":"easy","email":"easychen@gmail.com"}}
    """ 