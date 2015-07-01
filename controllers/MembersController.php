<?php
namespace Craft;

/**
 * OFFICIAL DOCUMENTATION:
 * http://buildwithcraft.com/docs/plugins/controllers
 */

class MembersController extends BaseController
{

	protected $allowAnonymous = array('actionLogin', 'actionLogout', 'actionGetAuthTimeout', 'actionForgotPassword', 'actionSendPasswordResetEmail', 'actionSendActivationEmail', 'actionSaveUser', 'actionSetPassword', 'actionVerifyEmail');

	/**
	 * Login User
	 */
	public function actionLogin()
	{
		if (craft()->userSession->isLoggedIn()) {
			$this->_handleSuccessfulLogin(false);
		}

		if (craft()->request->isPostRequest()) {
			craft()->users->purgeExpiredPendingUsers();
			$loginName = craft()->request->getPost('username');
			$password = craft()->request->getPost('password');
			$rememberMe = (bool) craft()->request->getPost('rememberMe');

			if (craft()->userSession->login($loginName, $password, $rememberMe)) {
				$this->_handleSuccessfulLogin(true);
			}	else {
				$errorCode = craft()->userSession->getLoginErrorCode();
				$errorMessage = craft()->userSession->getLoginErrorMessage($errorCode, $loginName);

				if (craft()->request->isAjaxRequest()) {
					$this->returnJson(array(
						'errorCode' => $errorCode,
						'error' => $errorMessage
					));
				} else {
					craft()->userSession->setError($errorMessage);
					craft()->urlManager->setRouteVariables(array(
						'loginName' => $loginName,
						'rememberMe' => $rememberMe,
						'errorCode' => $errorCode,
						'errorMessage' => $errorMessage,
					));
				}
			}
		}
	}



	/**
	 * Load Logged In User
	 */
	public function actionGetUser()
	{
		$profileId = craft()->request->getSegment(-1);
		$user = craft()->userSession->isLoggedIn();

		if ($user) {
			$currentUser = craft()->userSession->getUser();
			$currentUserId = $currentUser->id;
		}

		$this->renderTemplate('members/profile', array(
			'profileId' => $profileId,
			'user' => $user,
			'currentUser' => $currentUser
		));


		// craft()->urlManager->setRouteVariables(array(
		// 	'loginName' => $loginName,
		// 	'rememberMe' => $rememberMe,
		// 	'errorCode' => $errorCode,
		// 	'errorMessage' => $errorMessage,
		// ));
	}




	/**
	 * Process Successful Login
	 */
	private function _handleSuccessfulLogin($setNotice)
	{
		// Get the current user
		$currentUser = craft()->userSession->getUser();

		// Were they trying to access a URL beforehand?
		$returnUrl = craft()->userSession->getReturnUrl(null, true);

		if ($returnUrl === null || $returnUrl == craft()->request->getPath())	{
			// If this is a CP request and they can access the control panel, send them wherever
			// postCpLoginRedirect tells us
			if (craft()->request->isCpRequest() && $currentUser->can('accessCp')) {
				$postCpLoginRedirect = craft()->config->get('postCpLoginRedirect');
				$returnUrl = UrlHelper::getCpUrl($postCpLoginRedirect);
			}	else {
				// Otherwise send them wherever postLoginRedirect tells us
				$postLoginRedirect = craft()->config->get('postLoginRedirect');
				$returnUrl = UrlHelper::getSiteUrl($postLoginRedirect);
			}
		}

		// If this was an Ajax request, just return success:true
		if (craft()->request->isAjaxRequest()) {
			$this->returnJson(array(
				'success' => true,
				'returnUrl' => $returnUrl
			));
		}	else {
			if ($setNotice) {
				craft()->userSession->setNotice(Craft::t('Logged in.'));
			}

			$this->redirectToPostedUrl($currentUser, $returnUrl);
		}
	}



	/**
	 * For a normal form submission, send it here.
	 *
	 * HOW TO USE IT
	 * The HTML form in your template should include this hidden field:
	 *
	 *     <input type="hidden" name="action" value="members/exampleFormSubmit">
	 *
	 */
	public function actionExampleFormSubmit()
	{
		// ... whatever you want to do with the submitted data...
		$this->redirect('thankyou/page/url');
	}

	/**
	 * When you need AJAX, this is how to do it.
	 *
	 * HOW TO USE IT
	 * In your front-end JavaScript, POST your AJAX call like this:
	 *
	 *     // example uses jQuery
	 *     $.post('actions/members/exampleAjax' ...
	 *
	 * Or if your plugin is doing something within the control panel,
	 * you've got a built-in function available which Craft provides:
	 *
	 *     Craft.postActionRequest('members/exampleAjax' ...
	 *
	 */
	public function actionExampleAjax()
	{
		$this->requireAjaxRequest();
		// ... whatever your AJAX does...
		$response = array('response' => 'Round trip via AJAX!');
		$this->returnJson($response);
	}

	/**
	 * Routing lets you set extra variables when you load a Twig template.
	 *
	 * HOW TO USE IT
	 * Put this in your config/routes.php file:
	 *
	 *     'your/route' => array('action' => 'members/exampleRoute')
	 *
	 */
	public function actionExampleRoute()
	{
		// ... whatever your route accomplishes...
		$twigVariable = 'I added this with a route!';
		$this->renderTemplate('your/destination/template', array(
			'twigVariable' => $twigVariable
		));
	}

}
