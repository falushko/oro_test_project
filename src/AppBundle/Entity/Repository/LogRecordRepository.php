<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class LogRecordRepository extends EntityRepository
{
    public function getLogs(Request $request)
    {
        $limit = !empty($request->headers->get('limit')) ? $request->headers->get('limit') : 10;
        $offset = !empty($request->headers->get('offset')) ? $request->headers->get('offset') : 0;

        $logs = $this->createQueryBuilder('log')->select('log.datetime, log.record');

        /**
         * Applying datetime filters if they specified
         */
        if (!empty($request->get('datetime'))) {

            /**
             * Unique identifier for parameters placeholders
             */
            $i = 0;

            foreach ($request->get('datetime') as $datetime) {
                $logs = $logs
                    ->orWhere('log.datetime >= :start' . $i . ' AND log.datetime <= :end'. $i)
                    ->setParameter('start' . $i, \DateTime::createFromFormat('d#m#Y H#i#s', $datetime['start']))
                    ->setParameter('end' . $i, \DateTime::createFromFormat('d#m#Y H#i#s', $datetime['end']));

                $i++;
            }
        }

        /**
         * Applying text and/or regex filters if they provided
         */
        if (!empty($request->get('text')) && !empty($request->get('regex'))) {
            $logs = $logs
                ->andWhere('log.record LIKE :text')
                ->orWhere('REGEXP(log.record, :regex) = true')
                ->setParameter('text', '%' . $request->get('text') . '%')
                ->setParameter('regex', $request->get('regex'));

        } elseif (!empty($request->get('text'))) {
            $logs = $logs
                ->andWhere('log.record LIKE :text')
                ->setParameter('text', '%' . $request->get('text') . '%');

        } elseif (!empty($request->get('regex'))) {
            $logs = $logs
                ->andWhere('REGEXP(log.record, :regex) = true')
                ->setParameter('regex', $request->get('regex'));
        }

        $logs = $logs->orderBy('log.datetime', 'DESC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();

        $logs = $this->formatDates($logs);

        return $logs;
    }

    /**
     * @param $logs
     * @return array
     * Format datetime in database to timestamp.
     * I could use something like JMS Serializer for it bit I didn't.
     */
    private function formatDates($logs)
    {
        $result = [];

        foreach ($logs as $log) {
            $log['datetime'] = $log['datetime']->getTimestamp();
            $result[] = $log;
        }

        return $result;
    }
}