@component('mail::message')
# New Quote Request

A new quote request has been received from **{{ $quote->name }}**.

**Details:**
- **Name:** {{ $quote->name }}
- **Email:** {{ $quote->email }}
- **Phone:** {{ $quote->phone ?? 'Not provided' }}
- **Company:** {{ $quote->company ?? 'Not provided' }}
- **Project Type:** {{ ucfirst(str_replace('_', ' ', $quote->project_type)) }}
- **Budget Range:** {{ $quote->budget_range ? ucfirst(str_replace('_', ' - ', $quote->budget_range)) : 'Not specified' }}
- **Timeline:** {{ $quote->timeline }}

**Project Description:**
{{ $quote->description }}

@if($quote->goals)
**Project Goals:**
{{ $quote->goals }}
@endif

@if($quote->target_audience)
**Target Audience:**
{{ $quote->target_audience }}
@endif

@if($quote->additional_info)
**Additional Information:**
{{ $quote->additional_info }}
@endif

@component('mail::button', ['url' => url('/admin/quotes/' . $quote->id)])
View Full Request
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
