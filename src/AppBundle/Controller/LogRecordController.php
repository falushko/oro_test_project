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
     * @FOS\Post("log_records")
     *
     * {
     *      "datetime": [
     *          {
     *              "start" : "3/08/2005 14:00:45",
     *              "end" : "4/08/2005 12:56:34"
     *          },
     *          {
     *              "start": "3/08/2005 14:00:45",
     *              "end": "4/08/2005 12:56:34"
     *          },
     *          ...
     *      ],
     *
     *      "text" : "some text ololo",
     *      "regex" : "some regex"
     * }
     *
     * limit
     * offset
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function postLogRecordsAction(Request $request)
    {
        //$this->get('app.database.refresher')->updateLogRecordsWithNewLogs();

        $logs = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:LogRecord')
            ->getLogs($request);

        return new JsonResponse($logs, JsonResponse::HTTP_OK);
    }
}