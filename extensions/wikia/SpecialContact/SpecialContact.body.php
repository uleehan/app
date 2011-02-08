<?php
/**
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */
class ContactForm extends SpecialPage {
	var $mName, $mPassword, $mRetype, $mReturnto, $mCookieCheck, $mPosted;
	var $mAction, $mCreateaccount, $mCreateaccountMail, $mMailmypassword;
	var $mLoginattempt, $mRemember, $mEmail, $mBrowser;
	var $err, $errInputs;

	function  __construct() {
		parent::__construct( "Contact" , '' /*restriction*/);
		wfLoadExtensionMessages("ContactForm");
	}

	function execute() {
		global $wgLang, $wgAllowRealName, $wgRequest;
		global $wgOut, $wgExtensionsPath, $wgStyleVersion;
		global $wgUser;

		//$wgOut->addExtensionStyle("{$wgExtensionsPath}/wikia/SpecialContact/SpecialContact.css?{$wgStyleVersion}");
		$wgOut->addStyle( wfGetSassUrl( 'extensions/wikia/SpecialContact/SpecialContact.scss' ) );
		$this->mName = null;
		$this->mRealName = null;
		$this->mWhichWiki = null;
		$this->mProblem = $wgRequest->getText( 'wpContactSubject' ); //subject
		$this->mProblemDesc = null;
		$this->mPosted = $wgRequest->wasPosted();
		$this->mAction = $wgRequest->getVal( 'action' );
		$this->mEmail = $wgRequest->getText( 'wpEmail' );
		$this->mBrowser = $wgRequest->getText( 'wpBrowser' );
		$this->mCCme = $wgRequest->getCheck( 'wgCC' );

		if( $this->mPosted && ('submit' == $this->mAction ) ) {
			
			if( !$wgUser->isLoggedIn() ){
				$captchaObj = new FancyCaptcha();
				$captchaObj->retrieveCaptcha();
				$info = $captchaObj->retrieveCaptcha();
			}
			
			#ubrfzy note: these were moved inside to (lazy) prevent some stupid bots
			$this->mName = $wgRequest->getText( 'wpName' );
			$this->mRealName = $wgRequest->getText( 'wpContactRealName' );
			$this->mWhichWiki = $wgRequest->getText( 'wpContactWikiName' );
			#sibject still handled outside of post check, because of existing hardcoded prefill links
			$this->mProblemDesc = $wgRequest->getText( 'wpContactDesc' ); //body

			#malformed email?
			if (!User::isValidEmailAddr($this->mEmail)) {
				$this->err[].= wfMsg('invalidemailaddress');
				$this->errInputs['wpEmail'] = true;
			}

			#empty message text?
			if( empty($this->mProblemDesc) ) {
				$this->err[].= wfMsg('specialcontact-nomessage');
				$this->errInputs['wpContactDesc'] = true;
			}
			
			#captcha
			if(!$wgUser->isLoggedIn()){ // logged in users don't need the captcha (RT#139647)
				if(!( !empty($info) &&  $captchaObj->keyMatch( $wgRequest->getVal('wpCaptchaWord'), $info )))  {
					$this->err[].= wfMsg('specialcontact-captchafail');
					$this->errInputs['wpCaptchaWord'] = true; 
				}
			}

			#no errors?
			if( empty($this->err) )
			{
				#send email
				$this->processCreation();
				#stop here
				return;
			}

			#if there were any ->err s, they will be displayed in ContactForm
		}

		$this->mainContactForm();
	}
	/**
	 * @access private
	 */
	function processCreation() {
		global $wgUser, $wgOut, $wgCityId, $wgSpecialContactEmail;

		// If not configured, fall back to a default just in case.
		$wgSpecialContactEmail = (empty($wgSpecialContactEmail)?"community@wikia.com":$wgSpecialContactEmail);

		$wgOut->setPageTitle( wfMsg( 'specialcontact-pagetitle' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );

		//build common top of both emails
		$m_shared = '';
		$m_shared .= ( !empty($this->mRealName) )?( $this->mRealName ): ( (( !empty($this->mName) )?( $this->mName ): ('--')) );
		$m_shared .= " ({$this->mEmail})";
		$m_shared .= " ". (( !empty($this->mName) ) ? $this->mWhichWiki . "/wiki/User:" . urlencode(str_replace(" ", "_", $this->mName)) : $this->mWhichWiki) . "\n";
		$m_shared .= ( ( !empty($this->mProblem) ) ? "contacted Wikia about {$this->mProblem}.\n" : '' ). "";


		//start wikia debug info, sent only to the internal email, not cc'd
		$info = array();
		$info[] = '' . $this->mBrowser;
		$info[] = "\n" . 'IP:' . wfGetIP();
		$info[] = 'wkID: ' . $wgCityId;

		#global $wgAdminSkin, $wgDefaultSkin, $wgDefaultTheme;
		#$nominalSkin = ( !empty($wgAdminSkin) )?( $wgAdminSkin ):( ( !empty($wgDefaultTheme) )?("{$wgDefaultSkin}-{$wgDefaultTheme}"):($wgDefaultSkin) );
		#$info[] = 'Skin: ' . $nominalSkin;

		$uid = $wgUser->getID();
		if( !empty($uid) ) {
			$info[] = 'uID: ' . $uid . " (User:". $wgUser->getName() .")";
			$info[] = 'uLang: ' . $wgUser->getOption('language');
		}
		$info = implode("; ", $info) . "\n";
		//end wikia debug data

		$m = $m_shared . $info . "\n{$this->mProblemDesc}\n";

		if($this->mCCme) {
			$mcc = wfMsg('specialcontact-ccheader') . "\n\n";
			$mcc .= $m_shared . "\n{$this->mProblemDesc}\n";
		}

		$mail_user = new MailAddress($this->mEmail);
		$mail_community = new MailAddress($wgSpecialContactEmail);

		$errors = '';

		#to us, from user
		$subject = wfMsg('specialcontact-mailsub') . (( !empty($this->mProblem) )? ' - ' . $this->mProblem : '');
		$error = UserMailer::send( $mail_community, $mail_user, $subject, $m, $mail_user, null, 'SpecialContact' );
		if (WikiError::isError($error)) {
			$errors .= "\n" . $error->getMessage();
		}

		#to user, from us (but only if the first one didnt error, dont want to echo the user on an email we didnt get)
		if( empty($errors) && $this->mCCme && $wgUser->isEmailConfirmed() ) {
			$error = UserMailer::send( $mail_user, $mail_community, wfMsg('specialcontact-mailsubcc'), $mcc, $mail_user, null, 'SpecialContactCC' );
			if (WikiError::isError($error)) {
				$errors .= "\n" . $error->getMessage();
			}
		}

		if ( !empty($errors) ) {
			$this->addError( $this->err );
			//$wgOut->addHTML( Wikia::errorbox($errors) );
		}

		/********************************************************/
		#sending done, show message

		$wgOut->addHTML(wfMsg( 'specialcontact-submitcomplete' ));

		$mp = Title::newMainPage();
		$link = Xml::element('a', array('href'=>$mp->getLocalURL()), $mp->getPrefixedText());
		$wgOut->addHTML('<br/>' . wfMsg( 'returnto', $link ) );

		return;
	}
	/**
	 * @access private
	 */
	function mainContactForm( ) {
		global $wgUser, $wgOut, $wgLang;
		global $wgDBname, $wgAllowRealName;
		global $wgServer, $wgSitename;

		$wgOut->setPageTitle( wfMsg( 'specialcontact-pagetitle' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );

		if( !$wgUser->isLoggedIn() ){
			$captchaObj = new FancyCaptcha();
			$captcha = $captchaObj->pickImage();
			$captchaIndex = $captchaObj->storeCaptcha( $captcha );
			$titleObj = SpecialPage::getTitleFor( 'Captcha/image' );
			$captchaUrl = $titleObj->getLocalUrl( 'wpCaptchaId=' . urlencode( $captchaIndex ) );
		}
		
		if( $wgUser->isAnon() == false ) {
			//user mode

			//we have user data, so use it, overriding any passed in from url
			$this->mName = $wgUser->getName();
			$this->mRealName = $wgUser->getRealName();
			$this->mEmail = $wgUser->getEmail();

			# since logged in, assume...
			$user_readonly = true; //no box, just print
			$name_readonly = true;
			$mail_readonly = true;

			if( empty($this->mRealName) ) {
				#user has blank 'name', so unlock
				#(i disagree, but confuses users otherwise)
				$name_readonly = false;
			}

			if( empty($this->mEmail) ) {
				#user has blank email, so unlock
				$mail_readonly = false;
			}
		}
		else
		{
			global $wgCookiePrefix;

			#try to pull a username using an ancient method
			#-if this works, will fill name, but not cause lock
			if( !empty($_COOKIE[$wgCookiePrefix.'UserName']) ) {
				$this->mName = @$_COOKIE[$wgCookiePrefix.'UserName'];
			}

			#anon mode, no locks
			$user_readonly = false;
			$name_readonly = false;
			$mail_readonly = false;
		}

		$q = 'action=submit';

		$titleObj = Title::makeTitle( NS_SPECIAL, 'Contact' );
		$action = $titleObj->escapeLocalUrl( $q ) . "#contactform";

		$encName = htmlspecialchars( $this->mName );
		$encEmail = htmlspecialchars( $this->mEmail );
		$encRealName = htmlspecialchars( $this->mRealName );
		$encProblem = htmlspecialchars( $this->mProblem );
		$encProblemDesc = htmlspecialchars( $this->mProblemDesc );



		// add intro text
		$wgOut->addWikiText( wfMsg( 'specialcontact-intro' ) );

		$tabindex = 1;
		//setup form and javascript
		$wgOut->addHTML("<form name=\"contactform\" id=\"contactform\" method=\"post\" action=\"{$action}\">\n" );
		$wgOut->addHTML( "<h1>". wfMsg( 'specialcontact-formtitle' ) ."</h1>");
		
		if ( !empty($this->err) ) {
			$this->addError( $this->err );
			//$wgOut->addHTML( Wikia::errorbox( $this->err ) );
		}

		global $wgSpecialContactUnlockURL;
		$wgOut->addHTML( '<p class="contactformcaption">'  . wfMsg( 'specialcontact-wikiname' ) . '</p>' 
					. ( ( !empty($wgSpecialContactUnlockURL) ) ? 
						("<input ".$this->getClass('wpContactWikiName')." tabindex='" . ($tabindex++) . "' type='text' name=\"wpContactWikiName\" value=\"{$wgServer}\" size='40' />")
						:("{$wgServer} <input type=\"hidden\" name=\"wpContactWikiName\" value=\"{$wgServer}\" />")
					));


		$wgOut->addHTML( '<p class="contactformcaption">'  . wfMsg( 'specialcontact-username' ) . '</p>'
					. ( ( empty($user_readonly) ) ?
						("<input ".$this->getClass('wpName')."  tabindex='" . ($tabindex++) . "' type='text' name=\"wpName\" value=\"{$encName}\" size='40' />")
					:("{$encName} <input type=\"hidden\" name=\"wpName\" value=\"{$encName}\" />".
					" &nbsp;<span style=\"\" id='contact-not-me'><i><a href=\"/index.php?title=Special:UserLogout&amp;returnto=Special:Contact\">(". wfMsg( 'specialcontact-notyou', $this->mName ) .")</a></i></span>")
					));

		$wgOut->addHTML( '<p class="contactformcaption">'  . wfMsg( 'specialcontact-realname' ) . '</p>'
						. ( ( empty($name_readonly) )
					? ("<input ".$this->getClass('wpContactRealName')." tabindex='" . ($tabindex++) . "' type='text' name=\"wpContactRealName\" value=\"{$encRealName}\" size='40' />")
					:("{$encRealName} <input  type=\"hidden\" name=\"wpContactRealName\" value=\"{$encRealName}\" />")
					) );

		$wgOut->addHTML( '<p class="contactformcaption">'  . wfMsg( 'specialcontact-yourmail' ) . '</p>'
					. ( ( empty($mail_readonly) )
						? ("<input ".$this->getClass('wpEmail')." tabindex='" . ($tabindex++) . "' type='text' name=\"wpEmail\" value=\"{$encEmail}\" size='40' />")
						:("{$encEmail} <input  type=\"hidden\" name=\"wpEmail\" value=\"{$encEmail}\" />")
					));

		$wgOut->addHTML( '<p class="contactformcaption">'  . wfMsg( 'specialcontact-problem' ) . '</p>' 
							."<input ".$this->getClass('wpContactSubject')." tabindex='" . ($tabindex++) . "' type='text' name=\"wpContactSubject\" value=\"{$encProblem}\" size='80' />" );

		$wgOut->addHTML( '<p class="contactformcaption">'  . wfMsg( 'specialcontact-problemdesc' ) . '</p>' .
						"<textarea ".$this->getClass('wpContactDesc')." tabindex='" . ($tabindex++) . "' name=\"wpContactDesc\" rows=\"10\" cols=\"60\">{$encProblemDesc}</textarea>" );
		
		if( !$wgUser->isLoggedIn() ){
			$wgOut->addHTML('<p class="contactformcaption">'  . wfMsg( 'specialcontact-captchatitle' ) . '</p>' . 
							"<div class='contactCaptch' >
								<input ".$this->getClass('wpCaptchaWord')." type='text' id='wpCaptchaWord' name='wpCaptchaWord' value='' />
								<span class='captchDesc'>".wfMsg('specialcontact-captchainfo')."</span>
							</div>
							<img class='contactCaptch' width=150 height=70 src='".$captchaUrl."' />
							<input type='hidden' value='".$captchaIndex."' id='wpCaptchaId' name='wpCaptchaId'> " );
		}

		$wgOut->addHTML( "<p><input tabindex='" . ($tabindex++) . "' type='submit' value=\"". wfMsg( 'specialcontact-mail' ) ."\" /></p>" );

		if( !$wgUser->isAnon() && $wgUser->getEmail() != '') {
			//is user, has email, but is verified?
			if( $wgUser->isEmailConfirmed() ) {
				//yes!
				$wgOut->addHtml("<input tabindex='" . ($tabindex++) . "' type='checkbox' name=\"wgCC\" value=\"1\" />" . wfMsg('specialcontact-ccme') );
			}
			else
			{
				//not
				$wgOut->addHtml("<p><s><i>" . wfMsg('specialcontact-ccme') . "</i></s><br/> ". wfMsg('specialcontact-ccdisabled') ."</p>");
			}
		}

		#we prefil the browser info in from PHP var
		$wgOut->addHtml("
			<input type=\"hidden\" id=\"wpBrowser\" name=\"wpBrowser\" value=\"{$_SERVER['HTTP_USER_AGENT']}\" />
		</form>\n");

		#then, inside a javascript block, we add message that JS is enabled
		#result: when JS=off, we still get browser;
		#when JS=on, we get browser+JS=ON. win win.
		$wgOut->addHtml("\n<script type=\"text/javascript\">/*<![CDATA[*/
				var wpB = document.getElementById('wpBrowser');
				wpB.value = wpB.value + '; JavaScript Enabled';
			/*]]>*/</script>\n");

		return;
	}
	
	function addError($err) {
		global $wgOut;
		if(is_array($err)) { 
			$wgOut->addHTML('<div class="errorbox">');
				foreach($err as $value) {
					$wgOut->addHTML( $value . "<br>");
				}
			$wgOut->addHTML('</div><br style="clear: both;">');
		} else {
			$wgOut->addHTML( Wikia::errorbox( $err ) );	
		}
		
	}
	
	function getClass($id) {
		if(empty($this->errInputs[$id])) {
			return "";
		}
		return "class='error'";
	}
}