<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $user;
	public $password;

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$userinfo = User::model()->getUser($this->username);
        if(empty($userinfo)) { 
            $this->errorCode=self::ERROR_USERNAME_INVALID;  
        }else if($userinfo['password'] !== User::model()->hashPassword($this->password)) { 
            $this->errorCode=self::ERROR_PASSWORD_INVALID;  
        }else {
			Yii::app()->user->setState('userid', $userinfo['id']);
            Yii::app()->user->setState('username', $userinfo['username']);
            Yii::app()->user->setState('group', $userinfo['group']);
			$this->errorCode=self::ERROR_NONE;
		}
		return $this->errorCode==self::ERROR_NONE;
	}

}