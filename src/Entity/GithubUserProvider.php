<?php

namespace App\Entity;

use Doctrine\ORM\Query\Expr;
use Exception;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use App\Entity\User;

class GithubUserProvider
{

    private $client;
    private $serializer;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function loadUserByUserName($username, $userData)
    {
        $response = $this->client->get('https://api.github.com/user?access_token=' . $username);

        $result = $response->getBody()->getContents();

        if (!$userData) {
            throw new \LogicException('Did not managed to get your user info from github');
        }

        return  new User(
            $userData['login'],
            $userData['name'],
            $userData['email'],
            $userData['avatar_url'],
            $userData['html_url']
        );
    }
}
