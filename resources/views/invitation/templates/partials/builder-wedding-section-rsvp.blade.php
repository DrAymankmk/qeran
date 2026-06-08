@php
use App\Services\Invitation\WeddingInvitationPresenter;
$bc = $builderConfig ?? [];
$label = WeddingInvitationPresenter::blockValue($bc, 'rsvp', 'label', 'يرجى الرد');
$title = WeddingInvitationPresenter::blockValue($bc, 'rsvp', 'title', 'هل ستشاركنا؟');
$body = WeddingInvitationPresenter::blockValue($bc, 'rsvp', 'body', '');
$bs = WeddingInvitationPresenter::blockStyleAttributes($bc, 'rsvp');
@endphp
  <!-- ⑪ RSVP (dynamic) -->
  <div class="wi-rsvp-bg {{ $bs['class'] }}"@if($bs['style'] !== '') style="{{ $bs['style'] }}"@endif>
    <section class="wi-section">
      <p class="wi-section-label">{{ $label }}</p>
      <h2 class="wi-section-title">{{ $title }}</h2>
      @if($body !== '')
      <p class="wi-section-body">{{ $body }}</p>
      @endif
      <div class="wi-rsvp-form">
        <div class="wi-rsvp-toggle" id="rsvpToggle">
          <div class="wi-rsvp-opt active" onclick="setRsvp('yes',this)">أوافق بحب</div>
          <div class="wi-rsvp-opt" onclick="setRsvp('no',this)" style="border-left:0.5px solid rgba(200,169,122,0.3)">أعتذر</div>
        </div>
        <div class="wi-field"><label>الاسم الكامل</label><input type="text" placeholder="اسمك"></div>
        <div class="wi-field"><label>البريد الإلكتروني</label><input type="email" placeholder="your@email.com"></div>
        <div class="wi-field"><label>عدد الضيوف</label>
          <select>
            <option>أنا فقط</option>
            <option>2 ضيوف</option>
            <option>3 ضيوف</option>
            <option>4 ضيوف</option>
          </select>
        </div>
        <div class="wi-field" id="mealField"><label>تفضيل الوجبة</label>
          <select>
            <option>عادي</option>
            <option>نباتي</option>
            <option>نباتي صرف</option>
            <option>خالٍ من الغلوتين</option>
          </select>
        </div>
        <div class="wi-field"><label>ملاحظات أو قيود غذائية</label><textarea rows="3" placeholder="أي حساسية أو طلبات خاصة…"></textarea></div>
        <button class="wi-rsvp-submit" type="button" onclick="wiSubmitRsvpAccept()">تأكيد الحضور</button>
      </div>
    </section>
  </div>
