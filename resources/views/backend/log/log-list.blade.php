@extends('_layouts.backend')

@section('content')

    <div class="header-title">
        <span>后台操作日志</span>
    </div>
    <div class="tile-template">
        <form class="s_form" method="get" action="{{url('log')}}">
            <table>
                <tr>
                    <td class="col-title"><span>操作人：</span></td>
                    <td><input name="user_name" value="{{ isset($searchData['user_name']) ? $searchData['user_name'] : '' }}" type="text" /></td>
                    <td class="col-title"><span>操作模块：</span></td>
                    <td>
                        <select name="model">
                            <option value="">请选择</option>
                            @foreach( $modelList as $model )
                                <option value="{{ $model }}" @if(isset( $searchData['model']) && $model == $searchData['model'] ) selected="selected" @endif>{{ $model }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input class="s_btn" type="submit" value="查询" /></td>
                </tr>
            </table>
        </form>


 <div style="margin-bottom:0;" class="box bordered-box blue-border">
    <div class="box-content box-no-padding">
        <div class="">
            <div class="scrollable-area">
                <table style="margin-bottom:0;" class="data-table table table-hover table-bordered table-striped dataTable">
                    <thead>
                    <th>编号</th>
                    <th>操作人</th>
                    <th>操作人ip</th>
                    <th>操作模块</th>
                    <th>备注</th>
                    <th>操作时间</th>
                    {{--<th>操作</th>--}}
                    </thead>
                    <tbody>
                    @if(count($logList))
                        @foreach($logList as $log)
                            <tr>
                                <td>{{$log->id}}</td>
                                <td>{{$log->user_name}}</td>
                                <td>{{$log->ip}}</td>
                                <td>{{$log->model}}</td>
                                <td>{{$log->remark}}</td>
                                <td>{{date('Y-m-d H:i:s', $log->add_time)}}</td>
                                {{--<td>--}}
                                    {{--<a data-activityN="{{$log->id}}" class="editActivity">设置</a>--}}
                                {{--</td>--}}
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">
                                <img src="/images/no-data.png" alt="" style="margin: 20px 0 10px;">
                                <p>暂无记录</p>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div style="margin: 0 auto;text-align: center">{!! $logList->render() !!}</div>
                {{--{!! $logList->links() !!}--}}
            </div>
        </div>
    </div>
</div>
</div>


@endsection
@section('script')

@endsection