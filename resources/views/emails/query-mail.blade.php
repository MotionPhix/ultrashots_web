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

  <title>Thank you for contacting me!</title>

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
  Thank you for reaching out! I'll get back to you within 24 hours.
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
              </tr>
            </table>
          </td>
        </tr>

        <!-- Main content -->
        <tr>
          <td style="padding: 0 40px 40px 40px;" class="mobile-padding">

            <!-- Greeting -->
            <h1 style="margin: 0 0 24px 0; font-size: 28px; font-weight: 700; line-height: 1.2; color: #1a1a1a;" class="dark-mode-text">
              Thank you for contacting me!
            </h1>

            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #4a5568;" class="dark-mode-text-muted">
              Hi <strong style="color: #1a1a1a;" class="dark-mode-text">{{ $name }}</strong>,
            </p>

            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #4a5568;" class="dark-mode-text-muted">
              Thank you for getting in touch with me. I will provide you with a relevant response once I go through your message, usually within <strong>24 hours</strong>.
            </p>

            <p style="margin: 0 0 32px 0; font-size: 16px; line-height: 1.6; color: #4a5568;" class="dark-mode-text-muted">
              Should any of your information below be incorrect, please reply to this email with the corrected information so I can get back to you with proper feedback.
            </p>

            <!-- Contact information table -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 2px solid #e2e8f0; border-radius: 8px; overflow: hidden;" class="dark-mode-border">

              <!-- Table header -->
              <tr>
                <td colspan="2" style="padding: 16px 20px; background-color: #f7fafc; border-bottom: 1px solid #e2e8f0;" class="dark-mode-table-bg dark-mode-border">
                  <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #2d3748;" class="dark-mode-text">
                    📋 Contact Information
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
                  <a href="mailto:{{ $email }}" style="color: #3182ce; text-decoration: none;">{{ $email }}</a>
                </td>
              </tr>

              <!-- Phone -->
              <tr>
                <td style="padding: 16px 20px; {{ isset($company) ? 'border-bottom: 1px solid #e2e8f0;' : '' }} font-weight: 600; color: #4a5568;" class="{{ isset($company) ? 'dark-mode-border' : '' }} dark-mode-text-muted">
                  Phone Number
                </td>
                <td style="padding: 16px 20px; {{ isset($company) ? 'border-bottom: 1px solid #e2e8f0;' : '' }} color: #1a1a1a;" class="{{ isset($company) ? 'dark-mode-border' : '' }} dark-mode-text">
                  <a href="tel:{{ $phone }}" style="color: #3182ce; text-decoration: none;">{{ $phone }}</a>
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

            <!-- Call to action -->
            <div style="margin: 32px 0; padding: 20px; background-color: #f0f9ff; border-radius: 8px; border-left: 4px solid #3182ce;" class="dark-mode-table-bg">
              <p style="margin: 0; font-size: 14px; color: #1e40af;" class="dark-mode-text">
                💡 <strong>What's next?</strong> I'll review your message and get back to you with a personalized response. Feel free to reply to this email if you have any additional information to share.
              </p>
            </div>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="padding: 32px 40px; border-top: 1px solid #e2e8f0; background-color: #f7fafc;" class="mobile-padding dark-mode-border dark-mode-table-bg">

            <!-- Signature -->
            <p style="margin: 0 0 16px 0; font-size: 16px; color: #1a1a1a;" class="dark-mode-text">
              Best regards,<br>
              <strong>Kingsley Ultrashots</strong>
            </p>

            <!-- Copyright -->
            <p style="margin: 0 0 12px 0; font-size: 12px; color: #718096;" class="dark-mode-text-muted">
              © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>

            <!-- Unsubscribe notice -->
            <p style="margin: 0; font-size: 11px; color: #a0aec0; line-height: 1.4;" class="dark-mode-text-muted">
              You are receiving this email because you contacted me from
              <a href="{{ config('app.url') }}" style="color: #3182ce; text-decoration: none;"><strong>{{ config('app.name') }}</strong></a>.
              If you didn't mean to be contacted back, you can safely ignore and delete this email.
            </p>

          </td>
        </tr>

      </table>

    </td>
  </tr>
</table>

</body>
</html>
