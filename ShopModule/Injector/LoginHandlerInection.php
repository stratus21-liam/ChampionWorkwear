<?php
use SilverStripe\Security\MemberAuthenticator\LoginHandler;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Authenticator;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use App\Pages\DashboardPage;

class LoginHandlerInection extends LoginHandler{

    private static $allowed_actions = [
        'login',
        'LoginForm',
        'logout',
    ];

    protected function redirectAfterSuccessfulLogin()
    {
        $member      = Security::getCurrentUser();
        $dashboard   = DashboardPage::get()->First();

        $this
            ->getRequest()
            ->getSession()
            ->clear('SessionForms.MemberLoginForm.Email')
            ->clear('SessionForms.MemberLoginForm.Remember');

        if ($member->isPasswordExpired()) {
            return $this->redirectToChangePassword();
        }

        if( //if admin redirect to correct area
            $member->inGroup('administrators')
        ){ 
            return $this->redirect('/admin/pages');
        }   

        if( //if in following group codes then redirect to dashboard
            $member->IsAdmin == true
        ){ 

            return $this->redirect($dashboard->link());
        }

        if( //if in following group codes then redirect to dashboard
            $member->IsAdmin == false
        ){ 

            return $this->redirect('/');
        }        

        // Absolute redirection URLs may cause spoofing
        $backURL = $this->getBackURL();
        if ($backURL) {
            return $this->redirect($backURL);
        }

        // If a default login dest has been set, redirect to that.
        $defaultLoginDest = Security::config()->get('default_login_dest');
        if ($defaultLoginDest) {
            return $this->redirect($defaultLoginDest);
        }

        // Redirect the user to the page where they came from
        if ($member) {
            // Welcome message
            $message = _t(
                'SilverStripe\\Security\\Member.WELCOMEBACK',
                'Welcome back, {firstname}',
                ['firstname' => $member->FirstName]
            );
            Security::singleton()->setSessionMessage($message, ValidationResult::TYPE_GOOD);
        }

        // Redirect back
        return $this->redirectBack();
    }

}  



