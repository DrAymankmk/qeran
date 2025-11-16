{{--<!-- Modal -->--}}
{{--<div class="modal fade" style="display: none" id="{{$modalID}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog modal-lg" role="document">--}}
{{--        <form class="modal-content action_form" method="{{isset($modalMethod)?$modalMethod:'post'}}" action="{{ isset($modalRoute) ? $modalRoute : '' }}" enctype="multipart/form-data">--}}
{{--            @csrf--}}
{{--            {{ isset($modalMethodPutOrDelete) ? $modalMethodPutOrDelete : ''}}--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title" id="exampleModalLabel">{{$modalTitle}}</h5>--}}
{{--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                {{$modalContent}}--}}
{{--                <p>--}}
{{--                    هل حقا تريد حذف هذا العنصر من لوحة التحكم ٫ حيث لا يمكن إعادته مرة أخري بعد تأكيد الحذف--}}
{{--                </p>--}}

{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" class="btn btn-light" data-dismiss="modal">{{__('Close')}} <i class="fa fa-times"></i></button>--}}
{{--               @if(!isset($modalButtons))--}}
{{--                <button type="submit" class="btn btn-primary ">{{isset($modalButtonName)?$modalButtonName:__('Save')}}<i class="fa fa-check"></i></button>--}}
{{--               @endif--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </div>--}}
{{--</div>--}}
<div class="modal fade" style="display: none" id="{{$modalID}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="action_form" method="{{isset($modalMethod)?$modalMethod:'post'}}" action="{{ isset($modalRoute) ? $modalRoute : '' }}" enctype="multipart/form-data">
            @csrf
            {{ isset($modalMethodPutOrDelete) ? $modalMethodPutOrDelete : ''}}

            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">{{$modalTitle}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>
                    {{$modalContent}}

                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('admin.close')}}</button>
                <button type="submit" class="btn btn-primary">{{__('admin.confirm')}}</button>
            </div>
        </div>
        </form>
    </div>
</div>
