<?php

namespace App\DataFixtures;

use App\Document\TestSession;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

class SessionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sessionsData = [
            [
                'language' => 'TOEIC Listening and Reading',
                'startsAt' => new \DateTimeImmutable('2026-06-15 09:00:00'),
                'location' => 'Paris Centre - ETS Examination Hall',
                'capacity' => 25
            ],
            [
                'language' => 'TOEFL iBT',
                'startsAt' => new \DateTimeImmutable('2026-06-18 14:00:00'),
                'location' => 'Lyon - Campus Technologique',
                'capacity' => 15
            ],
            [
                'language' => 'IELTS Academic',
                'startsAt' => new \DateTimeImmutable('2026-06-20 10:30:00'),
                'location' => 'Marseille - Centre de Langues',
                'capacity' => 0
            ],
            [
                'language' => 'TOEIC Bridge',
                'startsAt' => new \DateTimeImmutable('2026-06-22 13:30:00'),
                'location' => 'Lille - Espace Multimédia',
                'capacity' => 40
            ],
            [
                'language' => 'TOEFL Essentials',
                'startsAt' => new \DateTimeImmutable('2026-06-25 16:00:00'),
                'location' => 'Bordeaux - Alliance Française',
                'capacity' => 8
            ]
        ];

        foreach ($sessionsData as $data) {
            $session = new TestSession(
                $data['language'],
                $data['startsAt'],
                $data['location'],
                $data['capacity']
            );

            $manager->persist($session);
        }

        $manager->flush();
    }
}