<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>OPTUS</title>
    </head>

    <body style="padding:0; margin:0;font:normal 14px arial;">
        <table style="width:100%;border: solid 2px #2B3643;border-collapse:collapse;">
            <tbody>
                <tr>
                    <td colspan="2" style="background:#2B3643;text-align:center;color:#ffffff;">
                        <img src="cid:front" alt="OPTUS Oportunidades de Mercado" title="OPTUS Oportunidades de Mercado" style="width:100px;margin:10px 0;">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="margin:10px;text-align:center;">
                            {$title}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="margin:10px;text-align:left;">
                            {block 'content'}{/block}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="color:#ffffff;background:#2B3643;">
                        <style>a:hover {literal}{color:#39A5B6 !important;}{/literal}</style>
                        <div style="margin:10px;text-align:left;">
                            <a href="{env('APP_SITE_URL')}" style="text-decoration:none;color:#ffffff;">
                                optus.com.ar
                            </a>
                        </div>
                    </td>
                    <td style="color:#ffffff;background:#2B3643;">
                        <div style="margin:10px;text-align:right;">{$ano} &copy; Optus by GCG</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>