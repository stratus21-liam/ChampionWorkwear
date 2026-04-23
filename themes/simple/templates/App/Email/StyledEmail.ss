<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>$Subject.XML</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#f5f5f5; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f5f5f5;">
        <tr>
            <td align="center" style="padding:30px 15px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:700px; background:#ffffff; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="background:#006892; padding:30px 20px; text-align:center;">
                            <% if $SiteLogo %>
                                <p style="margin:0 0 15px;">
                                    <img src="$SiteLogo" alt="$SiteConfig.Title.XML" style="max-width:220px; height:auto;">
                                </p>
                            <% end_if %>
                            <h1 style="margin:0; color:#ffffff; font-size:28px; line-height:1.2;">$Subject.XML</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 25px; color:#333333; font-size:16px; line-height:1.6;">
                            $Body
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 25px; background:#fafafa; color:#666666; font-size:14px; line-height:1.6; border-top:1px solid #e5e5e5;">
                            <% if $Footer %>
                                $Footer
                            <% else %>
                                <p style="margin:0 0 8px;">$SiteConfig.Title.XML</p>
                                <p style="margin:0;">You can log in here: <a href="$LoginURL" style="color:#006892;">Login</a></p>
                            <% end_if %>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>