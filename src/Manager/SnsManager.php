<?php

/*
 * This file is part of the SHQAwsSesBundle.
 *
 * Copyright Adamo Aerendir Crespi 2015 - 2017.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Adamo Aerendir Crespi <hello@aerendir.me>
 * @copyright Copyright (C) 2015 - 2017 Aerendir. All rights reserved.
 * @license   MIT License.
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Manager;

use Aws\Sns\SnsClient;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Entity\Topic;
use Symfony\Component\Routing\RouterInterface;

/**
 * Manages the interaction with AWS SNS.
 */
class SnsManager
{
    /** @var array $endpointConfig */
    private $endpointConfig;

    /** @var \Aws\Sns\SnsClient $client */
    private $client;

    /** @var RouterInterface $router */
    private $router;

    /**
     * @param array           $endpointConfig
     * @param SnsClient       $client
     * @param RouterInterface $router
     */
    public function __construct(array $endpointConfig, SnsClient $client, RouterInterface $router)
    {
        $this->endpointConfig = $endpointConfig;
        $this->client         = $client;
        $this->router         = $router;

        $this->router->getContext()->setHost($this->endpointConfig['host']);
        $this->router->getContext()->setScheme($this->endpointConfig['scheme']);
    }

    /**
     * @param string $topicName
     *
     * @return Topic
     */
    public function createTopic(string $topicName): Topic
    {
        // create SNS topic
        $topic    = ['Name' => $topicName];
        $response = $this->client->createTopic($topic);
        $topicArn = $response->get('TopicArn');

        $topic = new Topic($topicName, $topicArn);

        return $topic;
    }

    /**
     * Sets the App's endpoint in the SNS topic.
     *
     * Once set, the SNS topic will deliver all notification to this endpoint.
     *
     * @param string $topicArn
     *
     * @return string|null
     * @codeCoverageIgnore
     */
    public function setEndpoint(string $topicArn): ?string
    {
        $subscription = $this->buildSubscription($topicArn);
        $response     = $this->client->subscribe($subscription);

        return $response->get('SubscriptionArn');
    }

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getEndpointUrl(): string
    {
        return $this->router->generate('_shq_aws_ses_monitor_endpoint', [], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $topicArn
     *
     * @return array
     * @codeCoverageIgnore
     */
    private function buildSubscription(string $topicArn): array
    {
        return [
            'TopicArn'              => $topicArn,
            'Protocol'              => $this->endpointConfig['scheme'],
            'Endpoint'              => $this->getEndpointUrl(),
            'ReturnSubscriptionArn' => true,
        ];
    }
}
