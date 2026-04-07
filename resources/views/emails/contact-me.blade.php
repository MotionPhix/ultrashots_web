<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">

  <title>New Contact Form Submission from {{ $name }}</title>

  <!--[if mso]>
  <noscript>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
  </noscript>
  <![endif]-->

  <style>
    /* Reset and base styles */
    * {
      box-sizing: border-box;
    }

    body, table, td, p, a, li, blockquote {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }

    table, td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      -ms-interpolation-mode: bicubic;
      border: 0;
      height: auto;
      line-height: 100%;
      outline: none;
      text-decoration: none;
    }

    /* Client-specific styles */
    .ReadMsgBody { width: 100%; }
    .ExternalClass { width: 100%; }
    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
      line-height: 100%;
    }

    /* Dark mode styles */
    @media (prefers-color-scheme: dark) {
      .dark-mode-bg { background-color: #1a1a1a !important; }
      .dark-mode-text { color: #ffffff !important; }
      .dark-mode-text-muted { color: #cccccc !important; }
      .dark-mode-border { border-color: #333333 !important; }
      .dark-mode-table-bg { background-color: #2a2a2a !important; }
      .dark-mode-message-bg { background-color: #333333 !important; }
    }

    /* Mobile styles */
    @media screen and (max-width: 600px) {
      .mobile-full-width {
        width: 100% !important;
        max-width: 100% !important;
      }
      .mobile-padding {
        padding-left: 20px !important;
        padding-right: 20px !important;
      }
      .mobile-text-center {
        text-align: center !important;
      }
      .mobile-hide {
        display: none !important;
      }
    }
  </style>
</head>

<body style="margin: 0; padding: 0; width: 100%; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;" class="dark-mode-bg">

<!-- Preheader text -->
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
  New contact form submission from {{ $name }} - {{ Str::limit(strip_tags($user_query), 50) }}
</div>

<!-- Email wrapper -->
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa;" class="dark-mode-bg">
  <tr>
    <td align="center" style="padding: 40px 20px;">

      <!-- Main email container -->
      <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" class="mobile-full-width dark-mode-table-bg">

        <!-- Header with logo -->
        <tr>
          <td style="padding: 40px 40px 20px 40px; text-align: left;" class="mobile-padding">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr>
                <td>
                  <img src="{{ $message->embed($logo) }}" alt="{{ config('app.name') }} Logo" width="60" height="60" style="display: block; width: 60px; height: 60px; border-radius: 8px;">
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <span style="display: inline-block; padding: 6px 12px; background-color: #dc2626; color: #ffffff; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                      🚨 New Contact
                    </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Main content -->
        <tr>
          <td style="padding: 0 40px 40px 40px;" class="mobile-padding">

            <!-- Title -->
            <h1 style="margin: 0 0 24px 0; font-size: 28px; font-weight: 700; line-height: 1.2; color: #1a1a1a;" class="dark-mode-text">
              New message from {{ $name }}
            </h1>

            <!-- Timestamp -->
            <p style="margin: 0 0 24px 0; font-size: 14px; color: #718096; padding: 12px 16px; background-color: #f7fafc; border-radius: 6px; border-left: 4px solid #3182ce;" class="dark-mode-text-muted dark-mode-table-bg">
              📅 Received on {{ now()->format('F j, Y \a\t g:i A T') }}
            </p>

            <!-- Message content -->
            <div style="margin: 0 0 32px 0; padding: 24px; background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;" class="dark-mode-message-bg dark-mode-border">
              <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #374151;" class="dark-mode-text">
                💬 Message:
              </h3>
              <div style="font-size: 16px; line-height: 1.6; color: #1f2937; white-space: pre-wrap; word-wrap: break-word;" class="dark-mode-text">
                {!! nl2br(e($user_query)) !!}
              </div>
            </div>

            <!-- Contact information table -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 2px solid #e2e8f0; border-radius: 8px; overflow: hidden;" class="dark-mode-border">

              <!-- Table header -->
              <tr>
                <td colspan="2" style="padding: 16px 20px; background-color: #f7fafc; border-bottom: 1px solid #e2e8f0;" class="dark-mode-table-bg dark-mode-border">
                  <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #2d3748;" class="dark-mode-text">
                    👤 Contact Details
                  </h3>
                </td>
              </tr>

              <!-- Full Name -->
              <tr>
                <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #4a5568; width: 40%;" class="dark-mode-border dark-mode-text-muted">
                  Full Name
                </td>
                <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; color: #1a1a1a;" class="dark-mode-border dark-mode-text">
                  {{ $name }}
                </td>
              </tr>

              <!-- Email -->
              <tr>
                <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #4a5568;" class="dark-mode-border dark-mode-text-muted">
                  Email Address
                </td>
                <td style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; color: #1a1a1a;" class="dark-mode-border dark-mode-text">
                  <a href="mailto:{{ $email }}?subject=Re: Your inquiry&body=Hi {{ $name }},%0D%0A%0D%0AThank you for reaching out..." style="color: #3182ce; text-decoration: none; font-weight: 500;">{{ $email }}</a>
                </td>
              </tr>

              <!-- Phone -->
              <tr>
                <td style="padding: 16px 20px; {{ isset($company) ? 'border-bottom: 1px solid #e2e8f0;' : '' }} font-weight: 600; color: #4a5568;" class="{{ isset($company) ? 'dark-mode-border' : '' }} dark-mode-text-muted">
                  Phone Number
                </td>
                <td style="padding: 16px 20px; {{ isset($company) ? 'border-bottom: 1px solid #e2e8f0;' : '' }} color: #1a1a1a;" class="{{ isset($company) ? 'dark-mode-border' : '' }} dark-mode-text">
                  <a href="tel:{{ $phone }}" style="color: #3182ce; text-decoration: none; font-weight: 500;">{{ $phone }}</a>
                </td>
              </tr>

              <!-- Company (if provided) -->
              @isset($company)
                <tr>
                  <td style="padding: 16px 20px; font-weight: 600; color: #4a5568;" class="dark-mode-text-muted">
                    Company
                  </td>
                  <td style="padding: 16px 20px; color: #1a1a1a;" class="dark-mode-text">
                    {{ $company }}
                  </td>
                </tr>
              @endisset

            </table>

            <!-- Quick action buttons -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 32px 0;">
              <tr>
                <td style="padding: 0 8px 0 0;">
                  <a href="mailto:{{ $email }}?subject=Re: Your inquiry&body=Hi {{ $name }},%0D%0A%0D%0AThank you for reaching out..." style="display: inline-block; padding: 12px 24px; background-color: #3182ce; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">
                    📧 Reply via Email
                  </a>
                </td>
                <td style="padding: 0 0 0 8px;">
                  <a href="tel:{{ $phone }}" style="display: inline-block; padding: 12px 24px; background-color: #059669; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px;">
                    📞 Call {{ $name }}
                  </a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="padding: 32px 40px; border-top: 1px solid #e2e8f0; background-color: #f7fafc;" class="mobile-padding dark-mode-border dark-mode-table-bg">

            <!-- Admin notice -->
            <p style="margin: 0 0 16px 0; font-size: 14px; color: #4a5568; padding: 12px 16px; background-color: #fef3c7; border-radius: 6px; border-left: 4px solid #f59e0b;" class="dark-mode-text-muted">
              ⚡ <strong>Admin Notice:</strong> This email was automatically generated from your contact form. Please respond promptly to maintain good customer relations.
            </p>

            <!-- Copyright -->
            <p style="margin: 0; font-size: 12px; color: #718096;" class="dark-mode-text-muted">
              © {{ date('Y') }} {{ config('app.name') }}. All rights reserved. | Contact form notification system.
            </p>

          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>

</body>
</html>
