<?php

namespace App\Tests;

use App\Entity\User;

class RegistrationCest
{
    public const NEW_USER_CREDENTIALS = [
        'email' => 'new.user1@test-domain.com',
        'password' => 'new.user1'
    ];

    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('app_register');
    }

    public function registerNewUser(FunctionalTester $I)
    {
        $I->fillField('Email', self::NEW_USER_CREDENTIALS['email']);
        $I->fillField('Password', self::NEW_USER_CREDENTIALS['password']);
        $I->click('app.submit');
        /** @var User $userBeforeConfirmation */
        $userBeforeConfirmation = $I->grabEntityFromRepository(User::class, [
            'email' => self::NEW_USER_CREDENTIALS['email']
        ]);

        $I->assertNotNull($userBeforeConfirmation);
        $I->assertFalse($userBeforeConfirmation->isConfirmed());

        $I->amOnRoute('app_register_confirm', [
            'confirmationToken' => $userBeforeConfirmation->getConfirmationToken()
        ]);

        /** @var User $userAfterConfirmation */
        $userAfterConfirmation = $I->grabEntityFromRepository(User::class, [
            'email' => self::NEW_USER_CREDENTIALS['email']
        ]);

        $I->assertTrue($userAfterConfirmation->isConfirmed());

        $I->amOnRoute('app_login');
        $I->fillField('Email', self::NEW_USER_CREDENTIALS['email']);
        $I->fillField('Password', self::NEW_USER_CREDENTIALS['password']);
        $I->click('Sign in');
        $I->see('Hello UserController!');
    }
}
