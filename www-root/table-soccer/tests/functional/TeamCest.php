<?php

namespace App\Tests;

use App\DataFixtures\TeamFixtures;
use App\Entity\Team;
use App\Entity\User;

class TeamCest
{
    public const TEAM_SAMPLE_DATA = [
        'name' => 'Test Team 666',
        'description' => 'Description of Test Team'
    ];

    public function newTeam(FunctionalTester $I)
    {
        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('team_new');
        $I->fillField('Name', self::TEAM_SAMPLE_DATA['name']);
        $I->fillField('Description', self::TEAM_SAMPLE_DATA['description']);
        $I->click('Save');

        /** @var Team $team */
        $team = $I->grabEntityFromRepository(Team::class, [
            'name' => self::TEAM_SAMPLE_DATA['name']
        ]);

        $I->assertNotNull($team);
        $I->assertEquals(self::TEAM_SAMPLE_DATA['description'], $team->getDescription());
    }

    public function showTeam(FunctionalTester $I)
    {
        $this->createTestTeam($I);

        /** @var Team $teamFromDatabase */
        $teamFromDatabase = $I->grabEntityFromRepository(Team::class, [
            'name' => self::TEAM_SAMPLE_DATA['name']
        ]);

        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('team_show', ['id' => $teamFromDatabase->getId()]);

        $I->canSee($teamFromDatabase->getId());
        $I->canSee($teamFromDatabase->getName());
        $I->canSee($teamFromDatabase->getDescription());
    }

    public function editTeam(FunctionalTester $I) {
        $this->createTestTeam($I);

        /** @var Team $teamFromDatabase */
        $teamFromDatabase = $I->grabEntityFromRepository(Team::class, [
            'name' => self::TEAM_SAMPLE_DATA['name']
        ]);

        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('team_edit', ['id' => $teamFromDatabase->getId()]);

        $updatedName = self::TEAM_SAMPLE_DATA['name'] . ' updated';
        $updatedDescription = self::TEAM_SAMPLE_DATA['description'] . ' updated';
        $I->fillField('Name', $updatedName);
        $I->fillField('Description', $updatedDescription);
        $I->click('Update');

        /** @var Team $updatedTeamFromDatabase */
        $updatedTeamFromDatabase = $I->grabEntityFromRepository(Team::class, [
            'id' => $teamFromDatabase->getId()
        ]);

        $I->assertEquals($updatedName, $updatedTeamFromDatabase->getName());
        $I->assertEquals($updatedDescription, $updatedTeamFromDatabase->getDescription());
    }

    public function deleteTeamFromShowRoute(FunctionalTester $I) {
        $this->createTestTeam($I);

        /** @var Team $teamFromDatabase */
        $teamFromDatabase = $I->grabEntityFromRepository(Team::class, [
            'name' => self::TEAM_SAMPLE_DATA['name']
        ]);

        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('team_show', ['id' => $teamFromDatabase->getId()]);

        $I->click('app.delete');

//        TODO: Need to modify process of removing entity, use dialog instead of window.confirm
//        /** @var Team $updatedTeamFromDatabase */
//        $teamFromDatabase = $I->grabEntityFromRepository(Team::class, [
//            'id' => $teamFromDatabase->getId()
//        ]);
//
//        $I->assertEquals(null, $teamFromDatabase);
    }

    private function createTestTeam(FunctionalTester $I) {
        $team = new Team();
        $team->setName(self::TEAM_SAMPLE_DATA['name']);
        $team->setDescription(self::TEAM_SAMPLE_DATA['description']);
        $I->persistEntity($team);
        $I->flushToDatabase();
    }
}
