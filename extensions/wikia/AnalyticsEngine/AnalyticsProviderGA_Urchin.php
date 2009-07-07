<?php

class AnalyticsProviderGA_Urchin implements iAnalyticsProvider {


	public function getSetupHtml(){
		static $called = false;
		if ($called == true){
			return '';
		} else {
			$called = true;
		}

		return  '<script type="text/javascript" src="http://www.google-analytics.com/urchin.js"></script>' . "\n";
	}

	public function trackEvent($event, $eventDetails=array()){
		switch ($event){
		  case AnalyticsEngine::EVENT_PAGEVIEW:
			return '<script type="text/javascript">_uff=0;_uacct="UA-288915-1";urchinTracker();</script>';
		  case 'hub':
			if (empty($eventDetails['name'])){
				return '<!-- Missing category name  for hub tracking event -->';
			}
			$hub = "/" . str_replace(' ', '_', $eventDetails['name']);
			return '<script type="text/javascript">_uff=0;_uacct="UA-288915-2";urchinTracker("' .addslashes($hub).'");</script>';
		  case 'onewiki':
			return $this->onewiki($eventDetails[0]);

		  case 'pagetime':
			return $this->pagetime($eventDetails[0]);
		
                  default: return '<!-- Unsupported event for ' . __CLASS__ . ' -->';
		}
	}

	/* For certain wikis, we issue an additional call to track page views independently */
	private function onewiki($city_id){
		global $wgGoogleAnalyticsAccount;

		if (empty($wgGoogleAnalyticsAccount)){
			return '<!-- No tracking for this wiki -->';
		} else {
			return '<script type="text/javascript">_uff=0;_uacct="' . addslashes($wgGoogleAnalyticsAccount) . '";urchinTracker();</script>';
		}

	}

	/* Record how long the page took to load  */
	private function pagetime($skin){
		return '<script type="text/javascript">
			if (typeof window.wgNow == "object"){
				var now = new Date();
				var ms = (now.getTime() - window.wgNow.getTime()) / 1000;
     			        var pageTime = Math.floor(ms * 10) / 10; // Round to 1 decimal
				var slashtime = "/' . $skin . '/" + pageTime.toString().replace(/\./, "/");	
				_uff=0;
				_uacct="UA-288915-42";	
				urchinTracker(slashtime);
			}
			</script>';
	}

}
