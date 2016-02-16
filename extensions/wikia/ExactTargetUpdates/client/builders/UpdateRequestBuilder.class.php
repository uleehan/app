<?php
namespace Wikia\ExactTarget\Builders;

use Wikia\Util\Assert;

class UpdateRequestBuilder extends BaseRequestBuilder {
	const DATA_EXTENSION_OBJECT_TYPE = 'DataExtensionObject';

	const SAVE_OPTION_TYPE = 'SaveOption';
	const CUSTOMER_KEY_USER = 'user';
	const EXACT_TARGET_USER_ID_PROPERTY = 'user_id';

	private $userData;

	public function withUserData( array $userData ) {
		$this->userData = $userData;
		return $this;
	}

	/**
	 * @return \ExactTarget_UpdateRequest
	 */
	public function build() {
		$oRequest = new \ExactTarget_UpdateRequest();
		$oRequest->Options = $this->prepareUpdateCreateOptions();

		// prepare exact target structure
		$userObjects = $this->prepareUsersUpdateParams( $this->userData );
		// make it soap vars
		$oRequest->Objects = $this->prepareSoapVars( $userObjects, self::DATA_EXTENSION_OBJECT_TYPE );
		return $oRequest;
	}

	/**
	 * Creates an array of DataExtension objects for sending Soap update to ExactTarget
	 * @param array $aUsersData Array of users data for update. Each should contain at least array of user id and email.
	 * @return array An array of DataExtension objects
	 * @throws \Wikia\Util\AssertionException
	 */
	private function prepareUsersUpdateParams( array $aUsersData ) {
		$aDataExtension = [ ];
		foreach ( $aUsersData as $aUserData ) {
			//TODO: refactor to get rid of passing by reference
			$userId = $this->extractUserIdFromData( $aUserData );
			Assert::true( !empty( $userId ) );

			$oDE = new \ExactTarget_DataExtensionObject();
			$oDE->CustomerKey = self::CUSTOMER_KEY_USER;
			$oDE->Keys = [ $this->wrapApiProperty( self::EXACT_TARGET_USER_ID_PROPERTY, $userId ) ];

			$aApiProperties = [ ];
			foreach ( $aUserData as $sKey => $sValue ) {
				$aApiProperties[] = $this->wrapApiProperty( $sKey, $sValue );
			}
			$oDE->Properties = $aApiProperties;

			$aDataExtension[] = $oDE;
		}
		return $aDataExtension;
	}

	/**
	 * Returns user_id element from $aUserData array and removes it from array
	 * This for API params preparation. Allows to use user_id separately as key
	 * and user data as update parameters without user_id
	 * @param array $aUserData key value data from user table
	 * @return int
	 */
	private function extractUserIdFromData( &$aUserData ) {
		$iUserId = $aUserData[ self::EXACT_TARGET_USER_ID_PROPERTY ];
		unset( $aUserData[ self::EXACT_TARGET_USER_ID_PROPERTY ] );
		return $iUserId;
	}

	/**
	 * Returns ExactTarget_APIProperty object
	 * This object can be used as ExactTarget_DataExtensionObject property
	 * It stores key-value pair
	 * @param String $key Property name
	 * @param String $value Propert yvalue
	 * @return \ExactTarget_APIProperty
	 */
	private function wrapApiProperty( $key, $value ) {
		$apiProperty = new \ExactTarget_APIProperty();
		$apiProperty->Name = $key;
		$apiProperty->Value = $value;
		return $apiProperty;
	}

	/**
	 * Prepares ExactTarget_UpdateOptions that says update or create if doesn't exist
	 *
	 * @return \ExactTarget_UpdateOptions
	 */
	private function prepareUpdateCreateOptions() {
		$updateOptions = new \ExactTarget_UpdateOptions();

		$saveOption = new \ExactTarget_SaveOption();
		$saveOption->PropertyName = self::DATA_EXTENSION_OBJECT_TYPE;
		$saveOption->SaveAction = \ExactTarget_SaveAction::UpdateAdd;

		$updateOptions->SaveOptions = [ $this->wrapToSoapVar( $saveOption, self::SAVE_OPTION_TYPE ) ];
		return $updateOptions;
	}

}