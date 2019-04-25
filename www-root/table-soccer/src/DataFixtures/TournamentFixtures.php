<?php

namespace App\DataFixtures;

use App\Entity\Tournament;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TournamentFixtures extends Fixture
{
    public const TOURNAMENT_COUNT = 10;

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::TOURNAMENT_COUNT; $i++) {
            $tournament = new Tournament();
            $tournament->setName(
                sprintf('Tournament%02d', $i)
            );
            $tournament->setDescription(
                sprintf('Description of a Tournament%03d', $i)
            );

            $tournament->setStartDate(
                new \DateTime(sprintf('2019-%02d-01', $i))
            );

            for ($j = 1; $j <= 10; $j++) {
                $tournament->addTeam(
                    $this->getReference(
                        sprintf(TeamFixtures::TEAM_REFERENCE . '%d', $j)
                    )
                );
            }

            $manager->persist($tournament);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            TeamFixtures::class,
        );
    }
}
