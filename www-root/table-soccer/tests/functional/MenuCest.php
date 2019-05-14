<?php

namespace App\Tests;

use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\TournamentRepository;

class MenuCest
{

    public function moveThroughMenu(FunctionalTester $I)
    {
        $I->wantToTest('Menu for unauthorized user');
        $I->amOnRoute('app_home', []);
        $I->see('login');
        $I->see('register');
        $I->click('app.login');
        $I->see('app.sign_in');
        $I->click('app.register');
        $I->see('app.create_account');
        $I->dontSee('team_index');
        $I->dontSee('tournament_index');
        $I->dontSee('logout');

        $I->wantToTest('Menu for authorized user');
        $I->loginAsAdmin();
        $I->amOnRoute('app_home', []);
        $I->see('team_index');
        $I->see('tournament_index');
        $I->see('logout');
        $I->click('app.team_index');
        $I->see('team.index.title');
        $I->click('app.tournament_index');
        $I->see('tournament.index.title');
        $I->dontSee('login');
        $I->dontSee('register');
    }
}
