<?php

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    public const TEAM_REFERENCE = 'team';
    public const TEAM_COUNT = 100;

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::TEAM_COUNT; $i++) {
            $team = new Team();
            $team->setName(
                sprintf('Team%03d', $i)
            );
            $team->setDescription(
                sprintf('Description of a Team%03d', $i)
            );
            $manager->persist($team);
            $this->addReference(
                sprintf(self::TEAM_REFERENCE . '%d', $i),
                $team
            );
        }
        $manager->flush();
    }
}
