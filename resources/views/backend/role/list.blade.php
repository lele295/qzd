@extends('_layouts.backend')

@section('content')

    <div class="header-title">
        <span>角色列表</span>
    </div>
    <div class="tile-template">
 <div style="margin-bottom:0;" class="box bordered-box blue-border">
    <div class="box-content box-no-padding">
        <div class="">
            <div class="scrollable-area">
                <table style="margin-bottom:0;" class="data-table table table-hover table-bordered table-striped dataTable">
                    <thead>
                    <th>角色名称</th>
                    <th>拥有权限</th>
                    <th>权限描述</th>
                    <th>添加时间</th>
                    <th>操作</th>
                    </thead>
                    <tbody>
                    @foreach($data as $v)

                        <tr>
                            <td>{{$v['rolename']}}</td>
                            <td>{{$v['c']}}</td>
                            <td>{{$v['description']}}</td>
                            <td>{{date('Y-m-d h:i:s',$v['created_at'])}}</td>
                            <td><a href="#">编辑</a>|<a href="#">删除</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{--{!! $logList->links() !!}--}}
            </div>
        </div>
    </div>
</div>
</div>


@endsection
@section('script')

@endsection