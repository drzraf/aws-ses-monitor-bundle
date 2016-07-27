<?php

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Service;

use Aws\Credentials\Credentials;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository\TopicRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the confirmation of the subscription.
 *
 */
class SubscriptionConfirmationHandler implements HandlerInterface
{
    const HEADER_TYPE = 'SubscriptionConfirmation';

    /** @var  EntityManager $entityManager */
    private $entityManager;

    /** @var TopicRepositoryInterface */
    private $repo;

    /** @var AwsClientFactory $clientFactory*/
    private $clientFactory;

    /**
     * @param EntityManager $entityManager
     * @param ObjectRepository $repo
     * @param AwsClientFactory $clientFactory
     */
    public function __construct(EntityManager $entityManager, ObjectRepository $repo, AwsClientFactory $clientFactory)
    {
        $this->entityManager = $entityManager;
        $this->repo = $repo;
        $this->clientFactory = $clientFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(Request $request, Credentials $credentials)
    {
        if (!$request->isMethod('POST')) {
            return 405;
        }

        try {
            $data = json_decode($request->getContent(), true);
            $message = new Message($data);
            $validator = new MessageValidator();
            $validator->isValid($message);
        } catch (\Exception $e) {
            return 404;
        }

        if (isset($data['Token']) && isset($data['TopicArn'])) {
            $topicArn = $data['TopicArn'];
            $token = $data['Token'];

            $topicEntity = $this->repo->findOneByTopicArn($topicArn);
            if ($topicEntity instanceof Topic) {
                $topicEntity->setToken($token);
                $this->repo->save($topicEntity);

                $client = $this->clientFactory->getSnsClient($credentials);
                $client->confirmSubscription(
                    [
                        'TopicArn' => $topicEntity->getTopicArn(),
                        'Token' => $topicEntity->getToken()
                    ]
                );

                $this->repo->remove($topicEntity);

                return 200;
            }
        }

        return 404;
    }
}
