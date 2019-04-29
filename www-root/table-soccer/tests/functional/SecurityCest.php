<?php

namespace App\Tests;

use App\Entity\User;

class SecurityCest
{
    const TEST_USER_CREDENTIALS = [
        'email' => 'test1@test-domain.com',
        'password' => 'test1'
    ];

    public function _before(FunctionalTester $I)
    {
        $I->amOnRoute('app_login');
    }

    public function loginWithWrongCredentials(FunctionalTester $I)
    {
        // Same message for wrong email and wrong password is expected.
        $invalidCredentialsMessage = 'Invalid credentials.';

        // Let's add something to email so it won't be valid.
        $I->fillField('Email', 't' . self::TEST_USER_CREDENTIALS['email']);
        $I->fillField('Password', self::TEST_USER_CREDENTIALS['password']);
        $I->click('Sign in');
        $I->see($invalidCredentialsMessage);

        // Now let's try with invalid password.
        $I->fillField('Email', self::TEST_USER_CREDENTIALS['email']);
        $I->fillField('Password', 't' . self::TEST_USER_CREDENTIALS['password']);
        $I->click('Sign in');
        $I->see($invalidCredentialsMessage);
    }

    public function loginAsUser(FunctionalTester $I)
    {
        $I->fillField('Email', self::TEST_USER_CREDENTIALS['email']);
        $I->fillField('Password', self::TEST_USER_CREDENTIALS['password']);
        $I->click('Sign in');
        $I->see('Hello UserController!');
    }

    public function resetPassword(FunctionalTester $I)
    {
        // First, let's try to submit a request for a password reset.
        $I->amOnRoute('app_reset_password');
        $I->fillField('Email', self::TEST_USER_CREDENTIALS['email']);
        $I->click('app.send_reset_email');
        // TODO: Replace with actual translation
        $I->see('app.reset_email_sent');

        /** @var User $userWithPasswordResetRequest */
        $userWithPasswordResetRequest = $I->grabEntityFromRepository(User::class, [
            'email' => self::TEST_USER_CREDENTIALS['email']
        ]);

        $confirmationToken = $userWithPasswordResetRequest->getConfirmationToken();

        $I->amOnRoute('app_change_password', [
            'confirmationToken' => $confirmationToken
        ]);

        $newPassword = 'testPassword';
        $I->fillField('Password', $newPassword);
        $I->fillField('Repeat Password', $newPassword);
        $I->click('Submit');

        $I->amOnRoute('app_login');
        // Let's try login with new password
        $I->fillField('Email', self::TEST_USER_CREDENTIALS['email']);
        $I->fillField('Password', $newPassword);
        $I->click('Sign in');
        $I->see('Hello UserController!');

        $I->amOnRoute('app_logout');

        $I->amOnRoute('app_login');
        // For a good measure, let's try login with an old password
        $I->fillField('Email', self::TEST_USER_CREDENTIALS['email']);
        $I->fillField('Password', self::TEST_USER_CREDENTIALS['password']);
        $I->click('Sign in');
        $I->see('Invalid credentials.');
    }
}
