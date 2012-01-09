<?php
$config = array();

// Reskinned rich text editor
$config['rte'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#function_AssetsConfig::getRTEAssets'
	)
);
// Generic edit page JavaScript
$config['epl'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#function_AssetsConfig::getEPLAssets',
	)
);
// Generic edit page JavaScript + reskined rich text editor
$config['eplrte'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#group_epl',
		'#group_rte'
	)
);

// Site specific CSS
$config['site_anon_css'] = array(
	'type' => AssetsManager::TYPE_CSS,
	'assets' => array(
		'#function_AssetsConfig::getSiteCSS'
	)
);

$config['site_user_css'] = array(
	'type' => AssetsManager::TYPE_CSS,
	'assets' => array(
		'#group_site_anon_css',
	)
);

// WikiaScriptLoader
$config['wsl'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//skins/wikia/js/WikiaScriptLoader.js',
	)
);

// jQuery
$config['oasis_jquery'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#function_AssetsConfig::getJQueryUrl',
		'//skins/common/jquery/jquery.json-2.2.js',
		'//skins/common/jquery/jquery.getcss.js',
		'//skins/common/jquery/jquery.cookies.2.1.0.js',
		'//skins/common/jquery/jquery.timeago.js',
		'//skins/common/jquery/jquery.store.js',
		'//skins/common/jquery/jquery.wikia.js',
		'//skins/common/jquery/jquery.expirystorage.js',
		'//skins/oasis/js/tables.js',
		'//skins/oasis/js/common.js'
	)
);

// Oasis core shared JS
$config['oasis_shared_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#group_oasis_jquery',
		'//skins/common/wikibits.js',
		'//skins/common/mwsuggest.js',
		'//skins/common/ajax.js',
		'//skins/oasis/js/tracker.js',
		'//skins/common/jquery/jquery.wikia.modal.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker_config.js',
		'//extensions/wikia/WikiaTracker/js/WikiaLogger.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTrackerQueue.js',
		'//skins/common/jquery/jquery.wikia.tracker.js',
		'//skins/oasis/js/innerShiv.js',
		'//skins/oasis/js/hoverMenu.js',
		'//skins/oasis/js/PageHeader.js',
		'//skins/oasis/js/Search.js',
		'//skins/oasis/js/WikiaFooter.js',
		'//skins/oasis/js/buttons.js',
		'//skins/oasis/js/WikiHeader.js',
		'//skins/oasis/js/LatestPhotos.js',
		'//skins/oasis/js/Interlang.js',
		'//skins/oasis/js/WikiaNotifications.js',
		'//skins/oasis/js/Spotlights.js',
		'//skins/oasis/js/FirefoxFindFix.js',
		'//skins/oasis/js/isTouchScreen.js',
		'//skins/oasis/js/tabs.js',
		'//skins/oasis/js/SharingToolbar.js',
		'//skins/oasis/js/ContributeMenu.js',
	)
);

// Oasis extensions shared JS
$config['oasis_extensions_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//extensions/wikia/ShareFeature/js/ShareFeature.js',
		'//extensions/wikia/ArticleComments/js/ArticleComments.js',
		'//extensions/wikia/RelatedPages/js/RelatedPages.js',
		'//extensions/wikia/CreatePage/js/CreatePage.js',
		'//extensions/wikia/ImageLightbox/ImageLightbox.js',
		'//extensions/wikia/AjaxLogin/AjaxLoginBindings.js',
		'//extensions/FBConnect/fbconnect.js',
		'//extensions/wikia/Geo/geo.js',
		'//extensions/wikia/Meebo/meebo.js',
		'//skins/common/wikia/cookiecutter.js',
		'//extensions/wikia/RadiumOne/raon.js',
		'//extensions/wikia/VisualDNA/vdnaaat.js',
		'//extensions/wikia/AdEngine/AdMeldAPIClient.js',
		'//extensions/wikia/AdEngine/AdConfig.js',
		'//extensions/wikia/AdEngine/AdEngine.js',
		'//extensions/wikia/AdEngine/AdProviderOpenX.js',
		'//extensions/wikia/AdEngine/LazyLoadAds.js',
		'//extensions/wikia/AdEngine/ghost/gw-11.6.7/lib/gw.min.js',
		'//extensions/wikia/QuantcastSegments/qcs.js',
		'//extensions/wikia/ApertureAudience/Aperture.js',
		'//extensions/wikia/AdEngine/liftium/Liftium.js',
		'//extensions/wikia/AdEngine/liftium/Wikia.js',
		'//extensions/wikia/AdEngine/liftium/AdsInContent.js',
		'//extensions/wikia/AdEngine/AdDriver.js',
		'//extensions/wikia/AdSS/adss.js',
		'//extensions/wikia/PageLayoutBuilder/js/view.js', // TODO: load it on demand
		'//extensions/wikia/JSMessages/js/JSMessages.js', // TODO: maybe move to jquery.wikia.js
		'//extensions/wikia/GlobalNotification/GlobalNotification.js',
		'//skins/oasis/js/GlobalModal.js',	// This needs to load last after all common extensions, please keep this last.
	)
);

// Note: Owen moved getSiteJS call from both anon_js and user_js to OasisModule::loadJS
// so that common.js is loaded last so it has less chance of breaking other things

// Oasis anon JS
$config['oasis_anon_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//skins/oasis/js/LatestActivity.js',
		'//extensions/wikia/Interstitial/Exitstitial.js',
	)
);

// Oasis user JS
$config['oasis_user_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//skins/common/ajaxwatch.js',
		'//extensions/wikia/ArticleAjaxLoading/ArticleAjaxLoading.js',
	)
);

//Wikiaphone JS
$config['wikiaphone_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#group_oasis_jquery',
		'//skins/common/wikia/cookiecutter.js',
		'//extensions/wikia/RadiumOne/raon.js',
		'//extensions/wikia/AdEngine/AdConfig.js',
		'//extensions/wikia/WikiaTracker/js/WikiaLogger.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker_config.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTrackerQueue.js',
		'//skins/wikiaphone/main.js'
	)
);

//WikiaMobile JS, loaded at the top of the page in the head section
$config['wikiamobile_js_head'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//skins/common/zepto/zepto-0.8.js',
		'//skins/common/wikia/cookiecutter.js',
		'//extensions/wikia/RadiumOne/raon.js',
		'//extensions/wikia/AdEngine/AdConfig.js'
	)
);

//WikiaMobile JS, loaded at the bottom of the page in the body section
$config['wikiamobile_js_body'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//skins/common/wikia/outerhtml.js',
		'//skins/common/zepto/zepto.getcss.js',
		'//skins/common/zepto/zepto.wikiamobile.js',
		'//skins/common/zepto/zepto.modal.wikiamobile.js',
		'//extensions/wikia/WikiaTracker/js/WikiaLogger.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker_config.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTrackerQueue.js',
		'//extensions/wikia/WikiaMobile/js/WikiaMobile.js',
		'//extensions/wikia/JSMessages/js/JSMessages.wikiamobile.js',
		'//extensions/wikia/JSSnippets/js/JSSnippets.wikiamobile.js'
	)
);

//WikiaApp JS and CSS
$config['wikiaapp_css'] = array(
	'type' => AssetsManager::TYPE_CSS,
	'assets' => array(
		'//skins/wikiaapp/main.css',
		'//skins/wikiaapp/skin.css'
	)
);

$config['wikiaapp_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//skins//common/zepto/zepto-0.8.js',
		'//skins/wikiaapp/main.js'
	)
);

$config['chat_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#group_oasis_jquery',
		'//extensions/wikia/Chat/js/lib/socket.io.client.js',
		'//extensions/wikia/JSMessages/js/JSMessages.js',
		'//extensions/wikia/Chat/js/emoticons.js', // must be before controllers.js
		'//extensions/wikia/Chat/js/lib/underscore.js',
		'//extensions/wikia/Chat/js/lib/backbone.js',
		'//extensions/wikia/Chat/js/models/models.js',
		'//extensions/wikia/Chat/js/controllers/controllers.js',
		'//extensions/wikia/Chat/js/views/views.js',
	)
);

$config['theme_designer_js'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'#group_oasis_jquery',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker_config.js',
		'//extensions/wikia/WikiaTracker/js/WikiaLogger.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTrackerQueue.js',
		'//skins/common/jquery/jquery.wikia.tracker.js',
		'//skins/common/jquery/jquery-ui-1.8.14.custom.js',
		'//skins/common/jquery/jquery.wikia.tooltip.js',
		'//extensions/wikia/ThemeDesigner/js/ThemeDesigner.js',
		'//extensions/wikia/ThemeDesigner/js/aim.js',
	)
);

$config['yui'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//skins/common/yui_2.5.2/utilities/utilities.js',
		'//skins/common/yui_2.5.2/cookie/cookie-beta.js',
		'//skins/common/yui_2.5.2/container/container.js',
		'//skins/common/yui_2.5.2/autocomplete/autocomplete.js',
		'//skins/common/yui_2.5.2/animation/animation-min.js',
		'//skins/common/yui_2.5.2/logger/logger.js',
		'//skins/common/yui_2.5.2/menu/menu.js',
		'//skins/common/yui_2.5.2/tabview/tabview.js',
		'//skins/common/yui_2.5.2/slider/slider.js',
		'//skins/common/yui_extra/tools-min.js',
		'//skins/common/yui_extra/carousel-min.js',
	)
);

$config['photopop'] = array(
	'type' => AssetsManager::TYPE_JS,
	'assets' => array(
		'//extensions/wikia/PhotoPop/shared/lib/mustache.js',
		'//extensions/wikia/PhotoPop/shared/lib/my.class.js',
		'//extensions/wikia/PhotoPop/shared/lib/store.js',
		'//extensions/wikia/PhotoPop/shared/lib/observable.js',
		'//extensions/wikia/PhotoPop/shared/lib/reqwest.js',
		'//extensions/wikia/PhotoPop/shared/lib/classlist.js',
		'//extensions/wikia/PhotoPop/shared/lib/wikia.js',
		'//extensions/wikia/PhotoPop/shared/lib/require.js',
		'//extensions/wikia/WikiaTracker/js/WikiaLogger.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker_config.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTracker.js',
		'//extensions/wikia/WikiaTracker/js/WikiaTrackerQueue.js'
	)
);