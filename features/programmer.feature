Feature: Provide a consistent standard JSON API endpoint

	In order to build interchangeable front ends
	As a JSON API developer
	I need to allow Create, Read, Update, and Delete functionality

	Background:
	  # Imbo\BehatApiExtension\Context\ApiContext::setRequestHeader()
	  Given the "Content-Type" request header is "application/json"
	  And the user "weaverryan@google.com" exists

	Scenario: Can add a new Programmer
	  # Imbo\BehatApiExtension\Context\ApiContext::setRequestBody()
	  Given I have the request body:
      """
      {
        "nickname": "JavaProgrammer",
        "avatarNumber": 5,
        "user": "/api/users/%users|weaverryan@google.com|id%"
      }
      """
	  # Imbo\BehatApiExtension\Context\ApiContext::requestPath()
	  When I request for "api/programmers" using HTTP POST

	  # Imbo\BehatApiExtension\Context\ApiContext::assertResponseCodeIs()
	  Then the response code is 201
	  And the response should have "_links.self" property
	  And the response body contains JSON:
	  """
      {
	    "nickname": "JavaProgrammer",
	    "avatarNumber": 5,
	    "tagLine": null,
	    "powerLevel": 0
	  }
	  """