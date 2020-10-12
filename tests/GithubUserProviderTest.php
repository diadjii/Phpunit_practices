<?php


namespace App\Tests;

use App\Entity\GithubUserProvider;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use JMS\Serializer\Serializer;


class GithubUserProviderTest extends TestCase
{
  private $response;
  private $streamedResponse;
  private $client;

  public function setUp():void
  {
    $this->client = $this->getMockBuilder('GuzzleHttp\Client')
    ->disableOriginalConstructor()
    ->getMock();

    $this->response = $this->getMockBuilder('Psr\Http\Message\ResponseInterface')->getMock();
    $this->streamedResponse = $this->getMockBuilder('Psr\Http\Message\StreamInterface')->getMock();
   
    $this->client->expects($this->once())->method('get')->willReturn($this->response);

    $this->response
    ->expects($this->once())
    ->method('getBody')
    ->willReturn($this->streamedResponse);
  }

  public function testLoadUserByUsernameReturningAUser()
  {


    $userData = [
      'login' => 'my login',
      'name' => 'myusername',
      'email' => 'adre@mail.com',
      'avatar_url' => 'url to avatar',
      'html_url' => 'html url',
    ];

    $expectedUser = new User($userData['login'], $userData['name'], $userData['email'], $userData['avatar_url'], $userData['html_url']);

    $githubUserProvider = new GithubUserProvider($this->client);

    $user = $githubUserProvider->loadUserByUsername('an-access-token', $userData);

    $this->assertEquals($expectedUser, $user);
    $this->assertEquals('App\Entity\User', get_class($user));
  }

  public function testLoadUserByUsernameThrowingExecption()
  {
    $githubUserProvider = new GithubUserProvider($this->client);

    $user = $githubUserProvider->loadUserByUsername('an-access-token', []);

    $this->expectException('LogicException');
  }
}
