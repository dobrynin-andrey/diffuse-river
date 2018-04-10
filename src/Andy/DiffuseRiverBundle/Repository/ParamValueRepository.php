<?php

namespace Andy\DiffuseRiverBundle\Repository;
use Andy\DiffuseRiverBundle\Entity\Parameter;
use Andy\DiffuseRiverBundle\Entity\Point;

/**
 * ParamValueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ParamValueRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Позволяет получить значения параметра по точке и параметру
     *
     * @param Point $point
     * @param Parameter $parameter
     * @return array
     */

    public function getValueFromPointAndParameter(Point $point, Parameter $parameter) {

        return $this->getEntityManager()
            ->createQuery(
            'SELECT pv, pd.date, pd.id
                FROM AndyDiffuseRiverBundle:ParamValue pv
                JOIN AndyDiffuseRiverBundle:ParamDate pd
                WHERE pv.parameterId = :parameter 
                AND pd.pointId = :point
                AND pd.id = pv.paramDateId
                ORDER BY pd.date ASC'
            )
            ->setParameter('parameter', $parameter)
            ->setParameter('point', $point)
            ->getResult();
    }

    /**
     * Позволяет получить значения параметра по полю Код (code) и заданной точки
     *
     * @param Point $point
     * @param $parameterCode
     * @return mixed
     */
    public function getValueByCodeParameter(Point $point, $parameterCode) {
        return $this->getEntityManager()->createQueryBuilder()
            ->select( 'pr.name', 'pr.code', 'pr.edIzm', 'pv.value','pd.date')
            ->from('AndyDiffuseRiverBundle:ParamValue', 'pv')
            ->innerJoin(
                'AndyDiffuseRiverBundle:ParamDate',
                'pd',
                'WITH',
                'pd.id = pv.paramDateId'
            )
            ->innerJoin(
                'AndyDiffuseRiverBundle:Parameter',
                'pr',
                'WITH',
                'pr.id = pv.parameterId'
            )
            ->where('pd.pointId = :point')
            ->andWhere('pr.code = :code')
            ->setParameter('point', $point)
            ->setParameter('code', $parameterCode)
            ->orderBy('pd.date', 'asc')
            ->getQuery()->getResult();
    }
}