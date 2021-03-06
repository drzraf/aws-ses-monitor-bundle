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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Command\SesSendTestEmailsCommand;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * {@inheritdoc}
 */
class SesSendTestEmailsCommandTest extends TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $mockMailer  = self::getMockBuilder(\Swift_Mailer::class)->disableOriginalConstructor()->getMock();
        $mockMailer
            ->method('send')
            ->will(self::onConsecutiveCalls(1, 1, 1, 1, 0));

        /** @var \Swift_Mailer $mockMailer */
        $application->add(new SesSendTestEmailsCommand($mockMailer));

        /** @var ContainerAwareCommand $command */
        $command = $application->find('aws:ses:monitor:test:swiftmailer');

        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['test@example.com']);

        $commandTester->execute(['command'  => $command->getName()]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        self::assertContains('Sending an email from test@example.com to success@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to bounce@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to ooto@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to complaint@simulator.amazonses.com', $output);
        self::assertContains('Sending an email from test@example.com to suppressionlist@simulator.amazonses.com', $output);
    }
}
