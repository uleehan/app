/*global define, require, setTimeout*/
/*jshint maxlen:125, camelcase:false, maxdepth:7*/
define('ext.wikia.adEngine.provider.gpt.helper', [
	'ext.wikia.adEngine.adContext',
	'ext.wikia.adEngine.adLogicPageParams',
	'ext.wikia.adEngine.context.uapContext',
	'ext.wikia.adEngine.provider.gpt.adDetect',
	'ext.wikia.adEngine.provider.gpt.adElement',
	'ext.wikia.adEngine.provider.gpt.googleTag',
	'ext.wikia.adEngine.provider.gpt.googleSlots',
	'ext.wikia.adEngine.provider.gpt.targeting',
	'ext.wikia.adEngine.slot.service.passbackHandler',
	'ext.wikia.adEngine.slot.service.srcProvider',
	'ext.wikia.adEngine.slot.slotTargeting',
	'ext.wikia.adEngine.slotTweaker',
	'wikia.document',
	'wikia.instantGlobals',
	'wikia.log',
	require.optional('ext.wikia.adEngine.ml.rabbit'),
	require.optional('ext.wikia.adEngine.provider.gpt.sraHelper')
], function (
	adContext,
	adLogicPageParams,
	uapContext,
	adDetect,
	AdElement,
	googleTag,
	googleSlots,
	gptTargeting,
	passbackHandler,
	srcProvider,
	slotTargeting,
	slotTweaker,
	doc,
	instantGlobals,
	log,
	rabbit,
	sraHelper
) {
	'use strict';

	var logGroup = 'ext.wikia.adEngine.provider.gpt.helper',
		hiddenSlots = [
			'INCONTENT_PLAYER'
		];

	function isHiddenOnStart(slotName) {
		return hiddenSlots.indexOf(slotName) !== -1;
	}

	function getUapId() {
		var uapId = uapContext.getUapId();
		return uapId ? uapId.toString() : 'none';
	}

	/**
	 * Push ad to queue and flush if it should be
	 *
	 * @param {Object}  slot                   - slot (ext.wikia.adEngine.slot.adSlot::create instance)
	 * @param {string}  slotPath               - slot path
	 * @param {Object}  slotTargetingData      - slot targeting details
	 * @param {Object}  extra                  - optional parameters
	 * @param {boolean} extra.sraEnabled       - whether to use Single Request Architecture
	 * @param {string}  extra.forcedAdType     - ad type for callbacks info
	 */
	function pushAd(slot, slotPath, slotTargetingData, extra) {
		extra = extra || {};
		var element,
			slotName = slot.name;

		// copy value
		slotTargetingData = JSON.parse(JSON.stringify(slotTargetingData));

		if (isHiddenOnStart(slotName)) {
			slotTweaker.hide(slotName);
			slot.pre('success', function () {
				slotTweaker.show(slotName);
			});
		}

		setAdditionalTargeting(slotTargetingData);

		element = new AdElement(slotName, slotPath, slotTargetingData);

		function queueAd() {
			if (slot && slot.container) {
				log(['queueAd', slotName, element], log.levels.debug, logGroup);
				slot.container.appendChild(element.getNode());

				googleTag.addSlot(element);
			}
		}

		function setAdditionalTargeting(slotTargetingData) {
			var abId,
				rabbitResults = rabbit && rabbit.getResults(instantGlobals.wgAdDriverRabbitTargetingKeyValues);

			if (slotTargetingData.src) {
				slotTargetingData.src = srcProvider.get(slotTargetingData.src, extra);
			}

			if (rabbitResults && rabbitResults.length) {
				slotTargetingData.rabbit = rabbitResults;
			}

			slotTargetingData.passback = passbackHandler.get(slotName) || 'none';
			slotTargetingData.wsi = slotTargeting.getWikiaSlotId(slotName, slotTargetingData.src);
			slotTargetingData.uap = getUapId();
			slotTargetingData.outstream = slotTargeting.getOutstreamData() || 'none';
			if (adContext.get('targeting.skin') === 'oasis') {
				slotTargetingData.rail = doc.body.scrollWidth <= 1023 ? '0' : '1';
			}

			abId = slotTargeting.getAbTestId(slotTargetingData);
			if (abId) {
				slotTargetingData.abi = abId;
			}
		}

		function onAdLoadCallback(slotElementId, gptEvent, iframe) {
			// IE doesn't allow us to inspect GPT iframe at this point.
			// Let's launch our callback in a setTimeout instead.
			setTimeout(function () {
				log(['onAdLoadCallback', slotElementId], log.levels.info, logGroup);
				adDetect.onAdLoad(slot, gptEvent, iframe, extra.forcedAdType);
			}, 0);
		}

		function gptCallback(gptEvent) {
			log(['gptCallback', element.getId(), gptEvent], log.levels.info, logGroup);
			element.updateDataParams(gptEvent);
			googleTag.onAdLoad(slotName, element, gptEvent, onAdLoadCallback);
		}

		if (!googleTag.isInitialized()) {
			googleTag.init();
			googleTag.setPageLevelParams(adLogicPageParams.getPageLevelParams());
		}

		if (!slot.isEnabled()) {
			log(['Push blocked', slotName], log.levels.debug, logGroup);
			slot.collapse();
			return;
		}

		log(['pushAd', slotName, slotTargetingData], log.levels.info, logGroup);
		if (!slotTargetingData.flushOnly) {
			slot.pre('renderEnded', gptCallback);
			googleTag.push(queueAd);
		}

		if (!sraHelper || !extra.sraEnabled || sraHelper.shouldFlush(slotName)) {
			log('flushing', log.levels.debug, logGroup);
			googleTag.flush();
		}

		if (slotTargetingData.flushOnly) {
			log(['flushOnly - success', slotName], log.levels.debug, logGroup);
			slot.success();
		}
	}

	function refreshSlot(slotName) {
		var slot = googleSlots.getSlotByName(slotName),
			targeting;

		if (slot) {
			log(['Refresh slot', slotName, slot], log.levels.debug, logGroup);
			targeting = gptTargeting.getSlotLevelTargeting(slotName);
			targeting.uap = uapContext.getUapId().toString();
			AdElement.configureSlot(slot, targeting);
			googleTag.refreshSlot(slot);
		} else {
			log(['Refresh slot', slotName, 'does not exist'], log.levels.debug, logGroup);
		}
	}

	adContext.addCallback(function () {
		if (googleTag.isInitialized()) {
			googleTag.setPageLevelParams(adLogicPageParams.getPageLevelParams());
			uapContext.reset();
		}
	});

	return {
		refreshSlot: refreshSlot,
		pushAd: pushAd
	};
});
