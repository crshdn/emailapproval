<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Update</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0a0f1a;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0a0f1a;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background-color: #1e293b; border-radius: 16px; overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: <?= $action === 'Approved' ? '#065f46' : '#991b1b' ?>;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td>
                                        <h1 style="margin: 0; font-size: 24px; font-weight: 700; color: #ffffff;">
                                            <?= $action === 'Approved' ? '✓' : '✕' ?> Content <?= htmlspecialchars($action) ?>
                                        </h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 0 0 20px;">
                                        <p style="margin: 0 0 5px; font-size: 14px; color: #94a3b8;">Client</p>
                                        <p style="margin: 0; font-size: 18px; font-weight: 600; color: #f1f5f9;">
                                            <?= htmlspecialchars($clientName) ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 20px;">
                                        <p style="margin: 0 0 5px; font-size: 14px; color: #94a3b8;">Campaign</p>
                                        <p style="margin: 0; font-size: 16px; color: #f1f5f9;">
                                            <?= htmlspecialchars($campaignName) ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 0 0 20px;">
                                        <p style="margin: 0 0 5px; font-size: 14px; color: #94a3b8;">Type</p>
                                        <p style="margin: 0; font-size: 16px; color: #f1f5f9;">
                                            <?= ucfirst(str_replace('_', ' ', $itemType)) ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Content Preview -->
                            <div style="margin: 20px 0; padding: 20px; background-color: #0f172a; border-radius: 8px; border-left: 4px solid <?= $action === 'Approved' ? '#10b981' : '#ef4444' ?>;">
                                <p style="margin: 0 0 10px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #64748b;">
                                    Content Preview
                                </p>
                                <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #e2e8f0;">
                                    <?= htmlspecialchars($itemPreview) ?>
                                </p>
                            </div>
                            
                            <?php if (!empty($feedback)): ?>
                            <!-- Feedback -->
                            <div style="margin: 20px 0; padding: 20px; background-color: #451a03; border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <p style="margin: 0 0 10px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #fbbf24;">
                                    Client Feedback
                                </p>
                                <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #fef3c7;">
                                    <?= htmlspecialchars($feedback) ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 40px; background-color: #0f172a; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #475569;">
                                Email Approval System • <?= date('F j, Y \a\t g:i A') ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

