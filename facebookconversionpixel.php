<?php
/**
 * @package     Facebook Conversion Tracking Pixel and Custom Audience Pixel Plugin
 * @version 	0.1
 * @copyright   Copyright (C) 2015 Pablo Arias - http://www.pabloarias.eu
 * @license     Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class PlgSystemFacebookconversionpixel extends JPlugin
{
    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);
        $this->pixelid = $this->params->get('pixelid');
        $this->app = JFactory::getApplication();
    }

    function onBeforeCompileHead()
    {
        $doc = JFactory::getDocument();
        if ($this->app->isSite()) {
            $doc->addScriptDeclaration('
                !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                document,"script","//connect.facebook.net/en_US/fbevents.js");

                fbq("init", "' . $this->pixelid . '");
                fbq("track", "PageView");
            ');
            if ( $this->params->get('conversionmenuitem') == $this->app->getMenu()->getActive()->id ) {
                $doc->addScriptDeclaration('
                    fbq("track", "Lead");
                ');
            }
        }
    }
    
    function onAfterRender() {
        if ($this->app->isSite()) {
            $content = $this->app->getBody();
            $noscript = '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=' . $this->pixelid . '&amp;ev=PageView&amp;noscript=1" alt="Facebook pixel" /></noscript>';
            $finalcode = $noscript . '</body>';
            $finalcontent = str_replace('</body>', $finalcode, $content);
            $this->app->setBody($finalcontent);
        }
    }
}