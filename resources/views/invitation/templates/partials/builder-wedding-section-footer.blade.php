@php
use App\Services\Invitation\WeddingInvitationPresenter;

$bc = $builderConfig ?? [];

$dateLineDefault = '';
if (! empty($invitation?->date)) {
    try {
        $footerDate = \Carbon\Carbon::parse($invitation->date);
        $dateLineDefault = $footerDate->format('d · m · Y');
        $footerLocation = trim((string) ($wiVenueLocation ?? ''));
        if ($footerLocation !== '') {
            $dateLineDefault .= ' · '.$footerLocation;
        }
    } catch (\Throwable) {
        $dateLineDefault = '';
    }
}

$names = WeddingInvitationPresenter::blockValue($bc, 'footer', 'names', $wiNamesFooter ?? '');
$message = WeddingInvitationPresenter::blockValue(
    $bc,
    'footer',
    'message',
    "Thank you for being part of our love story.\nWe can't wait to celebrate with you."
);
$dateLine = WeddingInvitationPresenter::blockValue($bc, 'footer', 'date_line', $dateLineDefault);
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'footer');
@endphp
  <!-- ⑭ Thank You Footer -->
  <footer class="wi-footer {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <div class="wi-divider" style="margin: 0 auto 36px; width:180px;">
      <div class="wi-divider-diamond"></div>
    </div>
    @if($names !== '')
    <p class="wi-footer-names">{{ $names }}</p>
    @endif
    @if($message !== '')
    <p class="wi-footer-msg">{!! nl2br(e($message)) !!}</p>
    @endif
    @if($dateLine !== '')
    <p class="wi-footer-date">{{ $dateLine }}</p>
    @endif
  </footer>
  <!-- /⑭ Thank You Footer -->
