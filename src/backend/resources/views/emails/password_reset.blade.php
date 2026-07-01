<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.email_action') }}</title>
</head>
<body style="margin: 0; padding: 0; width: 100%; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f5; padding: 40px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; background-color: #ffffff; border: 1px solid #e4e4e7; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05); overflow: hidden;">
                    <!-- Header/Brand -->
                    <tr>
                        <td style="padding: 32px 32px 16px 32px; border-bottom: 1px solid #f4f4f5;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <span style="font-size: 24px; font-weight: 800; color: #18181b; letter-spacing: -0.02em;">GesPT</span>
                                        <span style="font-size: 12px; font-weight: 600; color: #71717a; text-transform: uppercase; margin-left: 8px; letter-spacing: 0.05em;">IPLeiria</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Content Body -->
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; font-weight: 700; color: #09090b; letter-spacing: -0.01em;">
                                {{ __('messages.email_greeting', ['name' => $user->name]) }}
                            </h1>
                            <p style="margin-top: 0; margin-bottom: 24px; font-size: 15px; line-height: 1.6; color: #3f3f46;">
                                {{ __('messages.email_intro') }}
                            </p>
                            
                            <!-- Action Button -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #09090b; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 28px; border-radius: 6px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 0.15s ease-in-out;">
                                            {{ __('messages.email_action') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin-top: 0; margin-bottom: 32px; font-size: 14px; line-height: 1.6; color: #71717a; font-style: italic;">
                                {{ __('messages.email_outro') }}
                            </p>
                            
                            <!-- Salutation -->
                            <p style="margin-top: 0; margin-bottom: 4px; font-size: 15px; font-weight: 500; color: #27272a;">
                                {{ __('messages.email_salutation') }}
                            </p>
                            <p style="margin-top: 0; margin-bottom: 0; font-size: 15px; font-weight: 600; color: #09090b;">
                                {{ __('messages.email_signature') }}
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Trouble URL fallback -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #fafafa; border-top: 1px solid #f4f4f5;">
                            <p style="margin-top: 0; margin-bottom: 8px; font-size: 12px; line-height: 1.5; color: #71717a;">
                                {{ __('messages.email_trouble') }}
                            </p>
                            <p style="margin-top: 0; margin-bottom: 0; font-size: 12px; word-break: break-all;">
                                <a href="{{ $resetUrl }}" target="_blank" style="color: #2563eb; text-decoration: underline;">
                                    {{ $resetUrl }}
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <!-- Footer -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 580px; margin-top: 24px;">
                    <tr>
                        <td align="center" style="font-size: 12px; color: #a1a1aa; line-height: 1.5; padding: 0 16px;">
                            &copy; {{ date('Y') }} GesPT &bull; IPLeiria Academic Platform. All rights reserved.<br>
                            This is an automated system email. Please do not reply.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
