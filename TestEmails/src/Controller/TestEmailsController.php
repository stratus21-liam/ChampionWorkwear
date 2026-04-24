<?php

namespace TestEmails\Controller;

use App\Email\Mailer;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\Security\SecurityToken;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class TestEmailsController extends Controller
{
    private static $allowed_actions = [
        'index' => true,
        'send' => true,
    ];

    protected function ensureCanAccess()
    {
        $member = Security::getCurrentUser();

        if (!$member || !Permission::checkMember($member, 'CMS_ACCESS')) {
            return Security::permissionFailure($this);
        }

        return null;
    }

    public function index(HTTPRequest $request): HTTPResponse
    {
        if ($response = $this->ensureCanAccess()) {
            return $response;
        }

        $status = (string) $request->getVar('status');
        $message = (string) $request->getVar('message');

        return HTTPResponse::create($this->buildPage($status, $message));
    }

    public function send(HTTPRequest $request)
    {
        if ($response = $this->ensureCanAccess()) {
            return $response;
        }

        // if (!$request->isPOST()) {
        //     return $this->httpError(405, 'Email test requests must use POST.');
        // }

        if (!SecurityToken::inst()->checkRequest($request)) {
            return $this->redirect($this->buildRedirectUrl(
                'error',
                'Your session expired. Please try again.'
            ));
        }

        $to = trim((string) $request->postVar('to'));
        $subject = trim((string) $request->postVar('subject'));
        $body = trim((string) $request->postVar('body'));

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return $this->redirect($this->buildRedirectUrl(
                'error',
                'Please enter a valid recipient email address.'
            ));
        }

        if ($subject === '') {
            return $this->redirect($this->buildRedirectUrl(
                'error',
                'Please enter an email subject.'
            ));
        }

        if ($body === '') {
            return $this->redirect($this->buildRedirectUrl(
                'error',
                'Please enter an email message.'
            ));
        }

        try {
            $result = Mailer::sendGenericEmail($to, $subject, nl2br(Convert::raw2xml($body)));
        } catch (TransportExceptionInterface | RfcComplianceException $exception) {
            return $this->redirect($this->buildRedirectUrl(
                'error',
                sprintf('Email could not be sent: %s', $exception->getMessage())
            ));
        }

        return $this->redirect($this->buildRedirectUrl(
            $result ? 'success' : 'error',
            $result
                ? sprintf('Test email sent to %s.', $to)
                : 'The email could not be sent.'
        ));
    }

    protected function buildRedirectUrl(string $status, string $message): string
    {
        return sprintf(
            '/dev/test-emails?status=%s&message=%s',
            rawurlencode($status),
            rawurlencode($message)
        );
    }

    protected function buildPage(string $status = '', string $message = ''): string
    {
        $tokenName = SecurityToken::inst()->getName();
        $tokenValue = SecurityToken::inst()->getValue();
        $statusMarkup = $this->buildStatusMarkup($status, $message);

        return sprintf(
            <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test Emails</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f5f1e8;
            --panel: #fffdf8;
            --text: #1f2933;
            --muted: #52606d;
            --border: #d9cbb3;
            --accent: #aa3a2a;
            --success-bg: #e8f5e9;
            --success-border: #7cb342;
            --error-bg: #fdecea;
            --error-border: #d64545;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 32px 16px;
            font-family: Georgia, "Times New Roman", serif;
            background:
                radial-gradient(circle at top left, rgba(170, 58, 42, 0.14), transparent 35%%),
                linear-gradient(180deg, #f8f4ec 0%%, var(--bg) 100%%);
            color: var(--text);
        }

        .wrap {
            max-width: 760px;
            margin: 0 auto;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 22px 55px rgba(31, 41, 51, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1;
        }

        p {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.5;
        }

        .status {
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid transparent;
            font-size: 0.98rem;
        }

        .status.success {
            background: var(--success-bg);
            border-color: var(--success-border);
        }

        .status.error {
            background: var(--error-bg);
            border-color: var(--error-border);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .field {
            margin-bottom: 18px;
        }

        input,
        textarea {
            width: 100%%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font: inherit;
            color: var(--text);
            background: #fff;
        }

        textarea {
            min-height: 220px;
            resize: vertical;
        }

        button {
            appearance: none;
            border: 0;
            border-radius: 999px;
            padding: 12px 18px;
            font: inherit;
            font-weight: 700;
            color: #fff;
            background: var(--accent);
            cursor: pointer;
        }

        button:hover {
            filter: brightness(1.05);
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="panel">
            <h1>Test Emails</h1>
            <p>Send a quick generic email through the site mailer to confirm delivery and template rendering are working.</p>
            %s
            <form method="post" action="/dev/test-emails/send">
                <input type="hidden" name="%s" value="%s">
                <div class="field">
                    <label for="to">To</label>
                    <input id="to" name="to" type="email" required>
                </div>
                <div class="field">
                    <label for="subject">Subject</label>
                    <input id="subject" name="subject" type="text" value="Test email from Champion" required>
                </div>
                <div class="field">
                    <label for="body">Message</label>
                    <textarea id="body" name="body" required>This is a test email sent from the TestEmails module.</textarea>
                </div>
                <button type="submit">Send test email</button>
            </form>
        </div>
    </div>
</body>
</html>
HTML,
            $statusMarkup,
            Convert::raw2att($tokenName),
            Convert::raw2att($tokenValue)
        );
    }

    protected function buildStatusMarkup(string $status, string $message): string
    {
        if ($status === '' || $message === '') {
            return '';
        }

        $class = $status === 'success' ? 'success' : 'error';

        return sprintf(
            '<div class="status %s">%s</div>',
            Convert::raw2att($class),
            Convert::raw2xml($message)
        );
    }
}
