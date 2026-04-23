<?php

namespace App\Email;

use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use SilverStripe\SiteConfig\SiteConfig;

class StyledEmail extends Email
{
    public function __construct($to, $subject)
    {
        parent::__construct();

        $this->setTo($to);
        $this->setSubject($subject);
        $this->setHTMLTemplate('App/Email/StyledEmail');
    }

    public function build($body = '', $footer = null, array $extraData = [])
    {
        $siteConfig = SiteConfig::current_site_config();
        $siteLogo = null;

        if (
            $siteConfig
            && $siteConfig->hasMethod('Logo')
            && $siteConfig->Logo()
            && $siteConfig->Logo()->exists()
        ) {
            $siteLogo = Director::absoluteURL($siteConfig->Logo()->getURL());
        }

        $this->setData(array_merge([
            'SiteConfig' => $siteConfig,
            'SiteLogo' => $siteLogo,
            'Subject' => $this->getSubject(),
            'Body' => $body,
            'Footer' => $footer,
            'LoginURL' => Director::absoluteURL('Security/Login'),
        ], $extraData));

        return $this;
    }
}