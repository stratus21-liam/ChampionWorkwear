<div style="text-align: center;">
    <div style="background-color:#f9e702; padding:30px 20px; color:#ffffff; margin-bottom:40px; border-radius:0px;">
        <h1 style="color:#000; margin:0; font-size:30px;">
            Password changed
        </h1>
    </div>
</div>

<div style="text-align: center;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="100%" style="padding-right:10px; text-align:left; vertical-align:top;">
                <h2 style="font-size:24px; margin:0 0 15px; color:#000;">
                    <%t SilverStripe\\Control\\ChangePasswordEmail_ss.HELLO 'Hi' %> $FirstName
                </h2>

                <p style="font-size:18px; line-height:1.6; color:#4f5052; margin:0 0 20px;">
                    <%t SilverStripe\\Control\\ChangePasswordEmail_ss.CHANGEPASSWORDTEXT1 'You changed your password for' is 'for a url' %>
                    $AbsoluteBaseURL.
                </p>

                <p style="font-size:18px; line-height:1.6; color:#4f5052; margin:0 0 20px;">
                    <%t SilverStripe\\Control\\ChangePasswordEmail_ss.CHANGEPASSWORDFOREMAIL2 'The password for account with email address {email} has been changed. If you did not change your password please change your password using the link below' email=$Email %>
                </p>

                <a
                    href="{$AbsoluteBaseURL}Security/changepassword"
                    style="display:inline-block; vertical-align:super; color:#000; padding:12px 22px; text-decoration:none; border-radius:0px; font-size:18px; font-weight:bold; background:#f9e702;"
                >
                    <%t SilverStripe\\Control\\ChangePasswordEmail_ss.CHANGEPASSWORDTEXT3 'Change password' %>
                </a>
            </td>
        </tr>
    </table>
</div>