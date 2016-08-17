<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOS;

/**
 * Class LogRecordController
 * @package AppBundle\Controller
 */
class LogRecordController extends FOSRestController
{
    /**
     * @FOS\Get("log_records")
     *
     * date [
     *  [start, end],
     *  [start, end]
     * ]
     *
     * text []
     * regexp []
     *
     * limit
     * offset
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLogRecordsAction(Request $request)
    {
        $this->get('app.database.refresher')->updateLogRecordsWithNewLogs();

        $logs = $this->get('doctrine.orm.entity_manager.abstract')
            ->getRepository('AppBundle:LogRecord')
            ->getLogs($request);

        return new JsonResponse([
            'title' => 'Success!',
            'logs' => $logs
        ], JsonResponse::HTTP_OK);
    }
}