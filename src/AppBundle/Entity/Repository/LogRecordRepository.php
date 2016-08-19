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

        //applying datetime filters if they specified
        if (!empty($request->get('datetime'))) {
            $i = 0; //unique identifier for parameters placeholders

            foreach ($request->get('datetime') as $datetime) {
                $logs = $logs
                    ->orWhere('log.datetime >= :start' . $i . ' AND log.datetime <= :end'. $i)
                    ->setParameter('start' . $i, \DateTime::createFromFormat('d#m#Y H#i#s', $datetime['start']))
                    ->setParameter('end' . $i, \DateTime::createFromFormat('d#m#Y H#i#s', $datetime['end']));

                $i++;
            }
        }

//        if (!empty($request->get('text'))) {
//
//        }
//
//        if (!empty($request->get('regex'))) {
//
//        }

        $logs = $logs->orderBy('log.datetime', 'DESC')
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();

        //todo format datetime with timestamps

        return $logs;
    }
}