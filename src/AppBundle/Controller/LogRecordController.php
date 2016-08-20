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
     * @api {post} /api/v1/log_records Get log records
     * @apiName Get log records
     * @apiVersion 1.0.0
     * @apiGroup Log
     *
     * @apiParam {array} datetime Array of objects with "start" and "end" fields in format "3/08/2005 14:00:45".
     * @apiParam {string} text Text for search.
     * @apiParam {string} regex Regular expression for search.
     * @apiParam {int} limit Limit. Passed in header.
     * @apiParam {int} offset Offset. Passed in header.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *              "datetime": 1204906202,
     *              "record": "record one"
     *         },
     *         {
     *              "datetime": 1110211802,
     *              "record": "record two"
     *         },
     *         ...
     *     ]
     *
     * @FOS\Post("log_records")
     * @param Request $request
     * @return JsonResponse
     */
    public function postLogRecordsAction(Request $request)
    {
        $this->get('app.database.refresher')->updateLogRecordsWithNewLogs();

        $logs = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:LogRecord')
            ->getLogs($request);

        return new JsonResponse($logs, JsonResponse::HTTP_OK);
    }
}