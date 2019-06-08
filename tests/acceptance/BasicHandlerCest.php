<?php 

class BasicHandlerCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryHttpOkTest(AcceptanceTester $I)
    {
    	$I->sendGet('/');
    	$I->seeResponseCodeIs(200);
    	$I->seeResponseContainsJson(['data' => 'Halo Dunia.']);
    }

    public function tryHttpNotFoundTest(AcceptanceTester $I)
    {
    	$I->sendGet('/' . mt_rand());
    	$I->seeResponseCodeIs(404);
    	$I->seeResponseContainsJson(['errors' => ['detail' => '404 Page Not Found']]);
    }
}
