@php

use App\Services\Invitation\WeddingInvitationPresenter;

$bc = $builderConfig ?? [];

$label = WeddingInvitationPresenter::blockValue($bc, 'rsvp', 'label', 'يرجى الرد');

$title = WeddingInvitationPresenter::blockValue($bc, 'rsvp', 'title', 'هل ستشاركنا؟');

$body = WeddingInvitationPresenter::blockValue($bc, 'rsvp', 'body', '');

$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'rsvp');

$rsvpAccepted = ($initialView ?? '') === 'success';

$rsvpDeclined = ($initialView ?? '') === 'decline';

@endphp

  <!-- ⑪ RSVP (dynamic) -->

  <div class="wi-rsvp-bg {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>

    <section class="wi-section">

      <p class="wi-section-label">{{ $label }}</p>

      <h2 class="wi-section-title">{{ $title }}</h2>

      @if($body !== '')

      <p class="wi-section-body">{{ $body }}</p>

      @endif



      <div class="wi-rsvp-form" id="wiRsvpForm">

        <div class="wi-rsvp-actions @if($rsvpAccepted || $rsvpDeclined) is-hidden @endif" id="wiRsvpActions">

          <button type="button" class="wi-rsvp-btn wi-rsvp-btn-accept" id="wiRsvpAcceptBtn">

            {{ __('admin.accept-invitation') }}

          </button>

          <button type="button" class="wi-rsvp-btn wi-rsvp-btn-decline" id="wiRsvpDeclineBtn">

            {{ __('admin.refuse-invitation') }}

          </button>

        </div>



        <div class="wi-rsvp-result wi-rsvp-result-accepted @if(!$rsvpAccepted) is-hidden @endif" id="wiRsvpAccepted">

          <p class="wi-rsvp-result-title">{{ __('admin.invitation-accepted-thanks') }}</p>

          <div class="wi-rsvp-qr-wrap">
            @if(!empty($user) && !empty($invitation))
            @include('invitation.partials.qr-section', [
              'invitation' => $invitation,
              'user' => $user,
              'wrapperClass' => 'wi-rsvp-qr',
            ])
            @else
            <p class="wi-rsvp-result-title">{{ __('admin.ib-preview-qr-hint') }}</p>
            @endif
          </div>

        </div>



        <div class="wi-rsvp-result wi-rsvp-result-declined @if(!$rsvpDeclined) is-hidden @endif" id="wiRsvpDeclined">

          <p class="wi-rsvp-result-title">{{ __('admin.invitation-declined-message') }}</p>

        </div>

      </div>

    </section>

  </div>


