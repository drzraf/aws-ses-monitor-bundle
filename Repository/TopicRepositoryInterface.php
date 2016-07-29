<?php

/*
 * This file is part of the AWS SES Monitor Bundle.
 *
 * (c) Adamo Aerendir Crespi.
 *
 * @author Adamo Aerendir Crespi <hello@aerendir.me>
 * @author Audrius Karabanovas <audrius@karabanovas.net>
 */

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Repository;

use SerendipityHQ\Bundle\AwsSesMonitorBundle\Model\Topic;

/**
 * Repository to manage a Topic.
 */
interface TopicRepositoryInterface
{
    /**
     * @param $topicArn
     *
     * @return Topic|null
     */
    public function findOneByTopicArn($topicArn);

    /**
     * @param Topic $topic
     *
     * @return mixed
     */
    public function save(Topic $topic);

    /**
     * @param Topic $topic
     *
     * @return mixed
     */
    public function remove(Topic $topic);
}
