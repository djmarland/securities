<?php

namespace SecuritiesService\Service;

use SecuritiesService\Data\Database\Entity\LSEAnnouncement as DbEntity;
use SecuritiesService\Domain\Entity\Enum\AnnouncementStatus;
use SecuritiesService\Domain\Entity\LSEAnnouncement;
use SecuritiesService\Domain\ValueObject\UUID;

class LSEAnnouncementsService extends Service
{
    const SERVICE_ENTITY = 'LSEAnnouncement';

    public function findByUUID(
        UUID $id
    ): LSEAnnouncement {
        return parent::simpleFindByUUID($id, self::SERVICE_ENTITY);
    }

    public function findLatest()
    {
        $since = $this->appTimeProvider->sub(new \DateInterval('P14D')); // 14 days

        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.status != :doneStatus')
            ->orWhere(self::TBL . '.dateFetched > :since')
            ->orderBy(self::TBL . '.dateFetched', 'DESC')
            ->setParameter('doneStatus', AnnouncementStatus::DONE)
            ->setParameter('since', $since);

        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function findIncomplete(
        int $limit,
        int $page = 1
    ): array {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.status != :status')
            ->orderBy(self::TBL . '.dateFetched', 'DESC')
            ->setParameter('status', AnnouncementStatus::DONE);

        $qb = $this->paginate($qb, $limit, $page);
        return $this->getDomainFromQuery($qb, self::SERVICE_ENTITY);
    }

    public function countIncomplete(): int
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select('count(' . self::TBL . '.id)')
            ->where(self::TBL . '.status != :status')
            ->setParameter('status', AnnouncementStatus::DONE);
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function markAsDone(LSEAnnouncement $announcement)
    {
        return $this->updateStatus($announcement,AnnouncementStatus::DONE);
    }

    public function markAsError(LSEAnnouncement $announcement)
    {
        return $this->updateStatus($announcement,AnnouncementStatus::ERROR);
    }

    private function updateStatus(LSEAnnouncement $announcement, $status)
    {
        $entity = $this->getEntityFromDomain($announcement);
        $entity->status = $status;
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    private function getEntityFromDomain(LSEAnnouncement $announcement): DbEntity
    {
        $qb = $this->getQueryBuilder(self::SERVICE_ENTITY);
        $qb->select(self::TBL)
            ->where(self::TBL . '.id = :id')
            ->setParameter('id', $announcement->getId()->getBinary());
        return $qb->getQuery()->getOneOrNullResult();
    }
}
