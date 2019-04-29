<?php
namespace App\Tests;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    public const TEST_ADMIN_CREDENTIALS = [
        'email' => 'admin@test-domain.com',
        'password' => 'admin'
    ];

   /**
    * Define custom actions here
    */
   public function loginAsAdmin() {
       $this->amHttpAuthenticated(self::TEST_ADMIN_CREDENTIALS['email'], self::TEST_ADMIN_CREDENTIALS['password']);
   }
}
