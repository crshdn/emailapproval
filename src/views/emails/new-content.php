<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Content Awaiting Approval</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0a0f1a;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0a0f1a;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background-color: #1e293b; border-radius: 16px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 30px; text-align: center; background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%);">
                            <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #ffffff;">New Content Awaiting Review</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 18px; color: #f1f5f9;">
                                Hello <?= htmlspecialchars($clientName) ?>,
                            </p>
                            
                            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #94a3b8;">
                                You have <strong style="color: #fbbf24;"><?= $pendingCount ?> item(s)</strong> waiting for your review and approval.
                            </p>
                            
                            <p style="margin: 0 0 30px; font-size: 16px; line-height: 1.6; color: #94a3b8;">
                                Please take a moment to review the content and provide your approval or feedback.
                            </p>
                            
                            <!-- CTA Button -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 30px 0;">
                                <tr>
                                    <td style="border-radius: 8px; background-color: #f59e0b;">
                                        <a href="<?= htmlspecialchars($portalUrl) ?>" target="_blank" style="display: inline-block; padding: 16px 32px; font-size: 16px; font-weight: 600; color: #1e293b; text-decoration: none;">
                                            Review Content Now
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <hr style="margin: 30px 0; border: none; border-top: 1px solid #334155;">
                            
                            <p style="margin: 0; font-size: 13px; color: #64748b;">
                                This is your private portal link. If you have any questions, please contact your account manager.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px; background-color: #0f172a; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #475569;">
                                &copy; <?= date('Y') ?> Email Approval System
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

