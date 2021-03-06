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

namespace SerendipityHQ\Bundle\AwsSesMonitorBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use SerendipityHQ\Bundle\AwsSesMonitorBundle\Processor\RequestProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * {@inheritdoc}
 */
class EndpointController extends Controller
{
    /**
     * @param Request                $request
     * @param RequestProcessor       $processor
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     * @codeCoverageIgnore
     */
    public function endpoint(Request $request, RequestProcessor $processor, EntityManagerInterface $entityManager): Response
    {
        $response = $processor->processRequest($request);

        $entityManager->flush();

        return $response;
    }
}
