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

  <title>New Quote Request from {{ $quote->name }}</title>

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
    * { box-sizing: border-box; }
    body, table, td, p, a, li, blockquote { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    .ReadMsgBody { width: 100%; }
    .ExternalClass { width: 100%; }
    .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }

    @media (prefers-color-scheme: dark) {
      .dark-mode-bg { background-color: #1a1a1a !important; }
      .dark-mode-text { color: #ffffff !important; }
      .dark-mode-text-muted { color: #cccccc !important; }
      .dark-mode-border { border-color: #333333 !important; }
      .dark-mode-table-bg { background-color: #2a2a2a !important; }
      .dark-mode-message-bg { background-color: #333333 !important; }
    }

    @media screen and (max-width: 600px) {
      .mobile-full-width { width: 100% !important; max-width: 100% !important; }
      .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
    }
  </style>
</head>

<body style="margin: 0; padding: 0; width: 100%; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;" class="dark-mode-bg">

<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
  New quote request from {{ $quote->name }} for {{ ucfirst(str_replace('_', ' ', $quote->project_type)) }}
</div>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8f9fa;" class="dark-mode-bg">
  <tr>
    <td align="center" style="padding: 40px 20px;">

      <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" class="mobile-full-width dark-mode-table-bg">

        <!-- Header -->
        <tr>
          <td style="padding: 40px 40px 20px 40px;" class="mobile-padding">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr>
                <td>
                  <img src="{{ $message->embed($logo) }}" alt="{{ config('app.name') }}" width="60" height="60" style="display: block; width: 60px; height: 60px; border-radius: 8px;">
                </td>
                <td style="text-align: right; vertical-align: top;">
                  <span style="display: inline-block; padding: 6px 12px; background-color: #7c3aed; color: #ffffff; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    📋 New Quote
                  </span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding: 0 40px 40px 40px;" class="mobile-padding">

            <h1 style="margin: 0 0 24px 0; font-size: 28px; font-weight: 700; line-height: 1.2; color: #1a1a1a;" class="dark-mode-text">
              Quote request from {{ $quote->name }}
            </h1>

            <p style="margin: 0 0 24px 0; font-size: 14px; color: #718096; padding: 12px 16px; background-color: #f7fafc; border-radius: 6px; border-left: 4px solid #7c3aed;" class="dark-mode-text-muted dark-mode-table-bg">
              📅 Received on {{ now()->format('F j, Y \a\t g:i A T') }}
            </p>

            <!-- Project Details -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 2px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 24px;" class="dark-mode-border">
              <tr>
                <td colspan="2" style="padding: 16px 20px; background-color: #f7fafc; border-bottom: 1px solid #e2e8f0;" class="dark-mode-table-bg dark-mode-border">
                  <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #2d3748;" class="dark-mode-text">📋 Project Details</h3>
                </td>
              </tr>
              <tr>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #4a5568; width: 40%;" class="dark-mode-border dark-mode-text-muted">Project Type</td>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #1a1a1a;" class="dark-mode-border dark-mode-text">{{ ucfirst(str_replace('_', ' ', $quote->project_type)) }}</td>
              </tr>
              @if($quote->budget_range)
              <tr>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #4a5568;" class="dark-mode-border dark-mode-text-muted">Budget Range</td>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #1a1a1a;" class="dark-mode-border dark-mode-text">{{ ucfirst(str_replace('_', ' - ', $quote->budget_range)) }}</td>
              </tr>
              @endif
              <tr>
                <td style="padding: 14px 20px; {{ $quote->goals || $quote->target_audience ? 'border-bottom: 1px solid #e2e8f0;' : '' }} font-weight: 600; color: #4a5568;" class="{{ $quote->goals || $quote->target_audience ? 'dark-mode-border' : '' }} dark-mode-text-muted">Timeline</td>
                <td style="padding: 14px 20px; {{ $quote->goals || $quote->target_audience ? 'border-bottom: 1px solid #e2e8f0;' : '' }} color: #1a1a1a;" class="{{ $quote->goals || $quote->target_audience ? 'dark-mode-border' : '' }} dark-mode-text">{{ $quote->timeline }}</td>
              </tr>
              @if($quote->goals)
              <tr>
                <td style="padding: 14px 20px; {{ $quote->target_audience ? 'border-bottom: 1px solid #e2e8f0;' : '' }} font-weight: 600; color: #4a5568; vertical-align: top;" class="{{ $quote->target_audience ? 'dark-mode-border' : '' }} dark-mode-text-muted">Goals</td>
                <td style="padding: 14px 20px; {{ $quote->target_audience ? 'border-bottom: 1px solid #e2e8f0;' : '' }} color: #1a1a1a;" class="{{ $quote->target_audience ? 'dark-mode-border' : '' }} dark-mode-text">{{ $quote->goals }}</td>
              </tr>
              @endif
              @if($quote->target_audience)
              <tr>
                <td style="padding: 14px 20px; font-weight: 600; color: #4a5568;" class="dark-mode-text-muted">Target Audience</td>
                <td style="padding: 14px 20px; color: #1a1a1a;" class="dark-mode-text">{{ $quote->target_audience }}</td>
              </tr>
              @endif
            </table>

            <!-- Description -->
            <div style="margin: 0 0 24px 0; padding: 24px; background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;" class="dark-mode-message-bg dark-mode-border">
              <h3 style="margin: 0 0 12px 0; font-size: 16px; font-weight: 600; color: #374151;" class="dark-mode-text">💬 Project Description</h3>
              <div style="font-size: 15px; line-height: 1.6; color: #1f2937; white-space: pre-wrap; word-wrap: break-word;" class="dark-mode-text">{!! nl2br(e($quote->description)) !!}</div>
            </div>

            <!-- Contact Details -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border: 2px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-bottom: 32px;" class="dark-mode-border">
              <tr>
                <td colspan="2" style="padding: 16px 20px; background-color: #f7fafc; border-bottom: 1px solid #e2e8f0;" class="dark-mode-table-bg dark-mode-border">
                  <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: #2d3748;" class="dark-mode-text">👤 Contact Details</h3>
                </td>
              </tr>
              <tr>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #4a5568; width: 40%;" class="dark-mode-border dark-mode-text-muted">Name</td>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #1a1a1a;" class="dark-mode-border dark-mode-text">{{ $quote->name }}</td>
              </tr>
              <tr>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #4a5568;" class="dark-mode-border dark-mode-text-muted">Email</td>
                <td style="padding: 14px 20px; border-bottom: 1px solid #e2e8f0; color: #1a1a1a;" class="dark-mode-border dark-mode-text">
                  <a href="mailto:{{ $quote->email }}" style="color: #7c3aed; text-decoration: none; font-weight: 500;">{{ $quote->email }}</a>
                </td>
              </tr>
              @if($quote->phone)
              <tr>
                <td style="padding: 14px 20px; {{ $quote->company ? 'border-bottom: 1px solid #e2e8f0;' : '' }} font-weight: 600; color: #4a5568;" class="{{ $quote->company ? 'dark-mode-border' : '' }} dark-mode-text-muted">Phone</td>
                <td style="padding: 14px 20px; {{ $quote->company ? 'border-bottom: 1px solid #e2e8f0;' : '' }} color: #1a1a1a;" class="{{ $quote->company ? 'dark-mode-border' : '' }} dark-mode-text">
                  <a href="tel:{{ $quote->phone }}" style="color: #7c3aed; text-decoration: none; font-weight: 500;">{{ $quote->phone }}</a>
                </td>
              </tr>
              @endif
              @if($quote->company)
              <tr>
                <td style="padding: 14px 20px; font-weight: 600; color: #4a5568;" class="dark-mode-text-muted">Company</td>
                <td style="padding: 14px 20px; color: #1a1a1a;" class="dark-mode-text">{{ $quote->company }}</td>
              </tr>
              @endif
            </table>

            <!-- CTA -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0">
              <tr>
                <td>
                  <a href="{{ url('/admin/quotes/' . $quote->id) }}" style="display: inline-block; padding: 14px 28px; background-color: #7c3aed; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px;">
                    View Full Request →
                  </a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="padding: 32px 40px; border-top: 1px solid #e2e8f0; background-color: #f7fafc;" class="mobile-padding dark-mode-border dark-mode-table-bg">
            <p style="margin: 0 0 12px 0; font-size: 14px; color: #4a5568; padding: 12px 16px; background-color: #fef3c7; border-radius: 6px; border-left: 4px solid #f59e0b;" class="dark-mode-text-muted">
              ⚡ <strong>Admin Notice:</strong> This notification was automatically generated. Please respond promptly.
            </p>
            <p style="margin: 0; font-size: 12px; color: #718096;" class="dark-mode-text-muted">
              © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
