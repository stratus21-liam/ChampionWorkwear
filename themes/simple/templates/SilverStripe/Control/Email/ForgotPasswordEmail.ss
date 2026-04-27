<div style="text-align: center;">
    <div style="background-color:#f9e702; padding:30px 20px; color:#ffffff; margin-bottom:40px; border-radius:0px;">
        <h1 style="color:#000; margin:0; font-size:30px;">
            Reset your password
        </h1>
    </div>
</div>

<div style="text-align: center;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="100%" style="padding-right:10px; text-align:left; vertical-align:top;">
                
                <h2 style="font-size:24px; margin:0 0 15px; color:#000;">
                    <%t SilverStripe\\Control\\Email\\ForgotPasswordEmail_ss.HELLO 'Hi' %> $FirstName
                </h2>

                <p style="font-size:18px; line-height:1.6; color:#4f5052; margin:0 0 20px;">
                    <%t SilverStripe\\Control\\Email\\ForgotPasswordEmail_ss.TEXT1 'Here is your' %>
                    <%t SilverStripe\\Control\\Email\\ForgotPasswordEmail_ss.TEXT2 'password reset link' %>
                    <%t SilverStripe\\Control\\Email\\ForgotPasswordEmail_ss.TEXT3 'for' %>
                    $AbsoluteBaseURL.
                </p>

                <!-- Button (email-safe) -->
                <table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
                    <tr>
                        <td style="background:#f9e702; border-radius:0px; text-align:center;">
                            <a
                                href="$AbsoluteBaseURL{$PasswordResetLink}"
                                style="display:inline-block; padding:12px 22px; color:#000; text-decoration:none; font-size:18px; font-weight:bold;"
                            >
                                Reset password
                            </a>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</div>

<div style="font-size:18px; color:#4f5052; margin-top:20px; line-height:1.6;">
    <p>
        <%t SilverStripe\\Control\\Email\\ForgotPasswordEmail_ss.TEXT4 'If the link above does not work, please copy and paste it into the address bar of your browser.' %>
    </p>

    <p style="word-break:break-word;">
        <a href="$PasswordResetLink" style="color:#000;">
            $PasswordResetLink
        </a>
    </p>
</div>