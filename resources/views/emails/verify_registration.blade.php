@extends('emails.template')

@section('content')

    <table class="es-content" align="center" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
        <tr style="border-collapse:collapse;">
            <td align="center" style="padding:0;Margin:0;">
                <table class="es-content-body" align="center" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;">
                    <tr style="border-collapse:collapse;">
                        <td align="left" style="padding:0;Margin:0;padding-top:20px;">
                            <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                <tr style="border-collapse:collapse;">
                                    <td align="center" width="600" valign="top" style="padding:0;Margin:0;">
                                        <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                            <tr style="border-collapse:collapse;">
                                                <td align="center" style="padding:0;Margin:0;padding-left:20px;padding-right:20px;"> <img class="adapt-img" src="https://ykast.stripocdn.email/content/guids/CABINET_57116f9afe83495a646cd7734bc77d26/images/39641523866414281.jpg" alt="Image" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;" title="Image" width="260"></td>
                                            </tr>
                                        </table> </td>
                                </tr>
                            </table> </td>
                    </tr>
                </table> </td>
        </tr>
    </table>
    <table class="es-content" align="center" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
        <tr style="border-collapse:collapse;">
            <td align="center" style="padding:0;Margin:0;">
                <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;border-left:1px solid transparent;border-right:1px solid transparent;border-top:1px solid transparent;border-bottom:1px solid transparent;" align="center" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
                    <tr style="border-collapse:collapse;">
                        <td align="left" style="Margin:0;padding-top:20px;padding-bottom:40px;padding-left:40px;padding-right:40px;">
                            <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                <tr style="border-collapse:collapse;">
                                    <td align="left" width="518" style="padding:0;Margin:0;">
                                        <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                            <tr style="border-collapse:collapse;">
                                                <td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-bottom:5px;"> <img src="https://ykast.stripocdn.email/content/guids/235eb5b6-4aee-47c9-840c-40977b40e1fd/images/32551561361316833.png" alt="icon" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;" title="icon" width="30"></td>
                                            </tr>
                                            <tr style="border-collapse:collapse;">
                                                <td class="es-m-txt-c" align="center" style="padding:0;Margin:0;"> <h2 style="Margin:0;line-height:29px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:24px;font-style:normal;font-weight:normal;color:#333333;">Hoşgeldiniz, {{ $name }}!</h2> </td>
                                            </tr>
                                            <tr style="border-collapse:collapse;">
                                                <td class="es-m-txt-c" align="center" style="padding:0;Margin:0;padding-top:15px;"> <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">Bu e-posta adresiyle bir Kolay Bütçe hesabı oluşturuldu. Eğer bu işlemi siz gerçekleştirdiyseniz lütfen aşağıdaki bağlantıya&nbsp;tıklayarak hesabınızı onaylayınız.</p> </td>
                                            </tr>
                                            <tr style="border-collapse:collapse;">
                                                <td align="center" style="Margin:0;padding-left:10px;padding-right:10px;padding-bottom:15px;padding-top:20px;"> <span class="es-button-border" style="border-style:solid;border-color:#474745;background:#474745;border-width:0px;display:inline-block;border-radius:20px;width:auto;"> <a href="{{ config('app.url') . '/verify/' . $verification_token }}" class="es-button" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:helvetica, 'helvetica neue', arial, verdana, sans-serif;font-size:16px;color:#EFEFEF;border-style:solid;border-color:#474745;border-width:6px 25px 6px 25px;display:inline-block;background:#474745;border-radius:20px;font-weight:normal;font-style:normal;line-height:19px;width:auto;text-align:center;">Hesabımı Onayla</a> </span> </td>
                                            </tr>
                                        </table> </td>
                                </tr>
                            </table> </td>
                    </tr>
                </table> </td>
        </tr>
    </table>
    <table class="es-content" align="center" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;">
        <tr style="border-collapse:collapse;">
        </tr>
        <tr style="border-collapse:collapse;">
            <td style="padding:0;Margin:0;background-color:#F7F7F7;" align="center" bgcolor="#f7f7f7">
                <table class="es-footer-body" align="center" width="600" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#F7F7F7;">
                    <tr style="border-collapse:collapse;">
                        <td align="left" style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px;">
                            <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                <tr style="border-collapse:collapse;">
                                    <td align="center" width="560" valign="top" style="padding:0;Margin:0;">
                                        <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                            <tr style="border-collapse:collapse;">
                                                <td align="center" style="padding:0;Margin:0;padding-bottom:5px;"> <h3 style="Margin:0;line-height:30px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:20px;font-style:normal;font-weight:normal;color:#333333;">Sosyal ağlarda bizi takip edin...</h3> </td>
                                            </tr>
                                            <tr style="border-collapse:collapse;">
                                                <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;">
                                                    <table class="es-table-not-adapt es-social" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;">
                                                        <tr style="border-collapse:collapse;">
                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:20px;"> <a href="https://kolaybutce.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Facebook" src="https://ykast.stripocdn.email/content/assets/img/social-icons/logo-black/facebook-logo-black.png" alt="Fb" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a> </td>
                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:20px;"> <a href="https://kolaybutce.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Youtube" src="https://ykast.stripocdn.email/content/assets/img/social-icons/logo-black/youtube-logo-black.png" alt="Yt" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a> </td>
                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:20px;"> <a href="https://kolaybutce.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Pinterest" src="https://ykast.stripocdn.email/content/assets/img/social-icons/logo-black/pinterest-logo-black.png" alt="P" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a> </td>
                                                            <td align="center" valign="top" style="padding:0;Margin:0;padding-right:20px;"> <a href="https://kolaybutce.com/" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Instagram" src="https://ykast.stripocdn.email/content/assets/img/social-icons/logo-black/instagram-logo-black.png" alt="Ig" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a> </td>
                                                            <td align="center" valign="top" style="padding:0;Margin:0;"> <a href="https://kolaybutce.com/" target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;"><img title="Twitter" src="https://ykast.stripocdn.email/content/assets/img/social-icons/logo-black/twitter-logo-black.png" alt="Tw" width="32" height="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;"></a> </td>
                                                        </tr>
                                                    </table> </td>
                                            </tr>
                                            <tr style="border-collapse:collapse;">
                                                <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">You are receiving this email because you have visited our site or asked us about the regular newsletter. You can&nbsp;<a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;line-height:21px;">unsubscribe here</a>&nbsp;or&nbsp;<a target="_blank" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:14px;text-decoration:underline;color:#333333;line-height:21px;">update your subscription preferences</a>.</p> </td>
                                            </tr>
                                            <tr style="border-collapse:collapse;">
                                                <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">© 2018</p> </td>
                                            </tr>
                                        </table> </td>
                                </tr>
                            </table> </td>
                    </tr>
                </table> </td>
        </tr>
    </table>

@endsection


