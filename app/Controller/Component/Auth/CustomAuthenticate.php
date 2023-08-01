<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');

class CustomAuthenticate extends BaseAuthenticate {
    public function authenticate(CakeRequest $request, CakeResponse $response) {
		return ClassRegistry::init('Usuario')->auth(
			$request->data['Usuario']['login'], 
			$request->data['Usuario']['password']
		);
    }
}
