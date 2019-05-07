<?php

namespace App\Tests;

use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\TournamentRepository;

class TournamentCest
{
    public const TOURNAMENT_SAMPLE_DATA = [
        'name' => 'Test Tournament 666',
        'description' => 'Description of Test Tournament',
        'startDate' => '2019-01-01',
    ];

    public function indexTournament(FunctionalTester $I)
    {
        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('tournament_index');
        // Let's see if by default we are on a first page
        $page = 1;
        /** @var Tournament[] $tournaments */
        $tournaments = $I->grabEntitiesFromRepository(Tournament::class);
        $tournamentsCount = count($tournaments);
        $I->canSee($tournaments[0]->getName());
        $supposedNumberOfPages = (int)($tournamentsCount / TournamentRepository::MAX_PER_PAGE) + ($tournamentsCount % TournamentRepository::MAX_PER_PAGE === 0 ? 0 : 1);
        while($page < $supposedNumberOfPages) {
            $I->click('Next');
            $I->canSeeInCurrentUrl((string)++$page);
            $indexToCheck = ($page - 1) * TournamentRepository::MAX_PER_PAGE;
            $I->assertTrue(array_key_exists($indexToCheck, $tournaments));
            $I->canSee($tournaments[$indexToCheck]->getName());
        }
    }

    public function newTournament(FunctionalTester $I)
    {
        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('tournament_new');
        $I->fillField('Name', self::TOURNAMENT_SAMPLE_DATA['name']);
        $I->fillField('Description', self::TOURNAMENT_SAMPLE_DATA['description']);
        $I->selectOption('Year', '2019');
        $I->selectOption('Month', 'Jan');
        $I->selectOption('Day', '1');
        $I->selectOption('Hour', '00');
        $I->selectOption('Minute', '00');
        $I->click('Save');

        /** @var Tournament $tournament */
        $tournament = $I->grabEntityFromRepository(Tournament::class, [
            'name' => self::TOURNAMENT_SAMPLE_DATA['name']
        ]);

        $I->assertNotNull($tournament);
        $I->assertEquals(self::TOURNAMENT_SAMPLE_DATA['description'], $tournament->getDescription());
    }

    public function showTournament(FunctionalTester $I)
    {
        $this->createTestTournament($I);

        /** @var Tournament $tournamentFromDatabase */
        $tournamentFromDatabase = $I->grabEntityFromRepository(Tournament::class, [
            'name' => self::TOURNAMENT_SAMPLE_DATA['name']
        ]);

        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('tournament_show', ['id' => $tournamentFromDatabase->getId()]);

        $I->canSee($tournamentFromDatabase->getId());
        $I->canSee($tournamentFromDatabase->getName());
        $I->canSee($tournamentFromDatabase->getDescription());
    }

    public function editTournament(FunctionalTester $I) {
        $this->createTestTournament($I);

        /** @var Tournament $tournamentFromDatabase */
        $tournamentFromDatabase = $I->grabEntityFromRepository(Tournament::class, [
            'name' => self::TOURNAMENT_SAMPLE_DATA['name']
        ]);

        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('tournament_edit', ['id' => $tournamentFromDatabase->getId()]);

        $updatedName = self::TOURNAMENT_SAMPLE_DATA['name'] . ' updated';
        $updatedDescription = self::TOURNAMENT_SAMPLE_DATA['description'] . ' updated';
        $I->fillField('Name', $updatedName);
        $I->fillField('Description', $updatedDescription);
        $I->click('Update');

        /** @var Tournament $updatedTournamentFromDatabase */
        $updatedTournamentFromDatabase = $I->grabEntityFromRepository(Tournament::class, [
            'id' => $tournamentFromDatabase->getId()
        ]);

        $I->assertEquals($updatedName, $updatedTournamentFromDatabase->getName());
        $I->assertEquals($updatedDescription, $updatedTournamentFromDatabase->getDescription());
    }

    public function deleteTournamentFromShowRoute(FunctionalTester $I) {
        $this->createTestTournament($I);

        /** @var Tournament $tournamentFromDatabase */
        $tournamentFromDatabase = $I->grabEntityFromRepository(Tournament::class, [
            'name' => self::TOURNAMENT_SAMPLE_DATA['name']
        ]);

        $I->loginAsAdmin();
        $I->am(User::ROLE_ADMIN);
        $I->amOnRoute('tournament_show', ['id' => $tournamentFromDatabase->getId()]);

        $I->click('app.delete');
    }

    private function createTestTournament(FunctionalTester $I) {
        $tournament = new Tournament();
        $tournament->setName(self::TOURNAMENT_SAMPLE_DATA['name']);
        $tournament->setDescription(self::TOURNAMENT_SAMPLE_DATA['description']);
        $tournament->setStartDate(new \DateTime(self::TOURNAMENT_SAMPLE_DATA['startDate']));
        $I->persistEntity($tournament);
        $I->flushToDatabase();
    }
}
